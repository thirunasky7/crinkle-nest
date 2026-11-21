<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\Cart;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected function baseQuery(Request $request)
    {
        if ($user = $request->user()) {
            return Order::where('user_id', $user->id);
        }

        return Order::whereIn('id', session('guest_orders', [0]));
    }

    public function index(Request $request)
    {
        $filter = in_array($request->query('filter'), ['all', 'active', 'delivered', 'custom'], true) ? $request->query('filter') : 'all';
        $base = $this->baseQuery($request);

        $activeOrder = (clone $base)->whereIn('status', Order::ACTIVE_STATUSES)->latest()->with('items')->first();

        $counts = [
            'all' => (clone $base)->count(),
            'active' => (clone $base)->whereIn('status', Order::ACTIVE_STATUSES)->count(),
            'delivered' => (clone $base)->where('status', 'delivered')->count(),
        ];

        $list = (clone $base)->with('items')->latest();
        match ($filter) {
            'active' => $list->whereIn('status', Order::ACTIVE_STATUSES),
            'delivered' => $list->where('status', 'delivered'),
            'custom' => $list->whereNotNull('label'),
            default => $activeOrder ? $list->where('id', '!=', $activeOrder->id) : null,
        };

        return view('pages.orders', [
            'activeOrder' => $activeOrder,
            'orders' => $list->take(50)->get(),
            'counts' => $counts,
            'filter' => $filter,
        ]);
    }

    protected function findOwned(Request $request, string $number): Order
    {
        $order = Order::where('order_number', $number)->with('items')->firstOrFail();
        $user = $request->user();
        $allowed = ($user && ($user->is_admin || $order->user_id === $user->id))
            || in_array($order->id, session('guest_orders', []), true);
        abort_unless($allowed, 403);

        return $order;
    }

    public function invoice(Request $request, string $number)
    {
        return view('pages.invoice', ['order' => $this->findOwned($request, $number)]);
    }

    public function reorder(Request $request, string $number)
    {
        $order = $this->findOwned($request, $number);
        $added = 0;
        foreach ($order->items as $item) {
            if ($item->product && $item->product->is_active) {
                Cart::add($item->product_id, $item->qty);
                $added++;
            }
        }

        return redirect()->route('cart.index')->with($added ? 'success' : 'error', $added ? 'Your previous batch is back in the basket.' : 'These bakes are no longer on the menu.');
    }

    public function rate(Request $request, string $number)
    {
        $order = $this->findOwned($request, $number);
        $data = $request->validate(['rating' => 'required|integer|min:1|max:5', 'review' => 'nullable|string|max:180']);
        $order->update($data);

        return back()->with('success', 'Thank you for rating your bakes!');
    }
}
