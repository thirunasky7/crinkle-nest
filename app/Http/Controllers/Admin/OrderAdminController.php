<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->withCount('items')->latest();
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($q = trim((string) $request->query('q'))) {
            $query->where(fn ($w) => $w->where('order_number', 'like', "%$q%")->orWhere('name', 'like', "%$q%")->orWhere('phone', 'like', "%$q%"));
        }

        return view('admin.orders.index', ['orders' => $query->paginate(25)->withQueryString()]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load('items', 'user')]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'payment_status' => ['required', Rule::in(array_keys(Order::PAYMENT_STATUSES))],
            'payment_note' => 'nullable|string|max:120',
            'target_arrival' => 'nullable|string|max:120',
            'status_note' => 'nullable|string|max:190',
            'tracking_url' => 'nullable|url|max:500',
            'label' => 'nullable|string|max:120',
        ]);

        $wasDelivered = $order->status === 'delivered';
        $order->fill($data);
        if ($order->status === 'delivered' && ! $wasDelivered) {
            $order->delivered_at = now();
            if ($order->user && ($per = (float) setting('points_per_100', 10)) > 0) {
                $order->user->increment('points', (int) floor((float) $order->total / 100 * $per));
            }
        }
        $order->save();

        return back()->with('success', 'Order updated.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }

    /* ---------- Contact messages ---------- */

    public function messages()
    {
        return view('admin.messages.index', ['messages' => ContactMessage::latest()->paginate(25)]);
    }

    public function message(ContactMessage $message)
    {
        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function destroyMessage(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }
}
