<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\DeliverySlot;
use App\Models\Order;
use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        seo()->page('cart')->noindex();
        $summary = Cart::summary();
        $inCart = $summary['lines']->pluck('product.id')->all();

        return view('pages.cart', [
            'summary' => $summary,
            'slots' => DeliverySlot::where('is_active', true)->orderBy('sort')->get(),
            'upsell' => Product::active()->where('is_upsell', true)->first(),
            'recommended' => Product::active()->where('is_recommended', true)->whereNotIn('id', $inCart)->take(6)->get(),
            'nextNumber' => setting('order_prefix', 'CN-').(88000 + (int) Order::max('id') + 1),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);
        $qty = max(1, (int) $request->input('qty', 1));
        Cart::add($product->id, $qty);
        track_event('add_to_cart', ['currency' => setting('currency_code', 'INR'), 'value' => (float) $product->price * $qty, 'items' => [self::gaItem($product, $qty)]]);

        return back()->with('success', $product->name.' added to your basket.');
    }

    /** GA4 ecommerce item. */
    public static function gaItem(Product $p, int $qty, ?float $price = null): array
    {
        return ['item_id' => (string) $p->id, 'item_name' => $p->name, 'item_category' => optional($p->category)->name, 'price' => (float) ($price ?? $p->price), 'quantity' => $qty];
    }

    public function update(Request $request, Product $product)
    {
        Cart::set($product->id, (int) $request->input('qty', 0));

        return redirect()->route('cart.index');
    }

    public function applyCoupon(Request $request)
    {
        $code = strtoupper(trim((string) $request->input('code')));
        if ($code === '') {
            return back();
        }
        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon) {
            return redirect()->route('cart.index')->with('error', 'That promo code was not found.');
        }
        Cart::setCoupon($code);
        $summary = Cart::summary();
        if ($summary['coupon_error'] && $summary['subtotal'] > 0) {
            return redirect()->route('cart.index')->with('error', $summary['coupon_error']);
        }

        return redirect()->route('cart.index')->with('success', 'Code '.$code.' saved to your basket.');
    }

    public function removeCoupon()
    {
        Cart::setCoupon(null);

        return redirect()->route('cart.index');
    }

    public function checkout(Request $request)
    {
        $summary = Cart::summary();
        if ($summary['lines']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your basket is empty.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:150',
            'instructions' => 'nullable|string|max:255',
            'slot_id' => 'nullable|exists:delivery_slots,id',
            'custom_date' => 'nullable|date|after:today',
            'gift_note' => 'nullable|string|max:500',
            'payment_method' => 'required|in:whatsapp,online',
        ]);

        $slot = isset($data['slot_id']) ? DeliverySlot::find($data['slot_id']) : null;
        if ($slot && $slot->is_custom && empty($data['custom_date'])) {
            return back()->withInput()->withErrors(['custom_date' => 'Please choose a date for your custom slot.']);
        }

        $user = $request->user();
        $order = DB::transaction(function () use ($data, $summary, $slot, $user) {
            $order = Order::create([
                'order_number' => 'TMP-'.uniqid(),
                'user_id' => $user?->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'],
                'city' => $data['city'],
                'instructions' => $data['instructions'] ?? null,
                'slot_label' => $slot ? $slot->fullLabel() : null,
                'custom_date' => $slot && $slot->is_custom ? ($data['custom_date'] ?? null) : null,
                'gift_note' => $data['gift_note'] ?? null,
                'subtotal' => $summary['subtotal'],
                'delivery_fee' => $summary['delivery_fee'],
                'discount' => $summary['discount'],
                'coupon_code' => $summary['coupon']?->code,
                'total' => $summary['total'],
                'status' => 'confirmed',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'label' => $slot && $slot->is_custom ? 'Custom Bake' : null,
            ] + attribution());

            foreach ($summary['lines'] as $line) {
                $order->items()->create([
                    'product_id' => $line->product->id,
                    'name' => $line->product->name,
                    'detail' => $line->product->detail_line ?: $line->product->short_description,
                    'image' => $line->product->image,
                    'price' => $line->product->price,
                    'qty' => $line->qty,
                    'total' => $line->total,
                ]);
            }

            if ($summary['coupon']) {
                $summary['coupon']->increment('used_count');
            }

            if ($user && ! $user->addresses()->exists()) {
                Address::create([
                    'user_id' => $user->id, 'label' => 'Home', 'icon' => 'home',
                    'line1' => $data['address'], 'line2' => $data['city'],
                    'recipient' => $data['name'], 'phone' => $data['phone'],
                    'note' => $data['instructions'] ?? null, 'is_default' => true,
                ]);
            }

            return $order->fresh('items');
        });

        Cart::clear();
        track_event('purchase', [
            'transaction_id' => $order->order_number,
            'value' => (float) $order->total,
            'currency' => setting('currency_code', 'INR'),
            'shipping' => (float) $order->delivery_fee,
            'coupon' => $order->coupon_code,
            'payment_type' => $order->payment_method,
            'items' => $summary['lines']->map(fn ($l) => self::gaItem($l->product, $l->qty))->values()->all(),
        ]);
        if (! $user) {
            session()->push('guest_orders', $order->id);
        }

        if ($data['payment_method'] === 'whatsapp') {
            $lines = $order->items->map(fn ($i) => '• '.$i->name.' x'.$i->qty.' = '.money($i->total))->implode("\n");
            $message = 'Hi '.setting('site_name', 'Crinkle Nest').", I would like to confirm my order #{$order->order_number}"
                .($order->slot_label ? ' for '.$order->slot_label : '').".\n{$lines}\nTotal: ".money($order->total)
                ."\nDeliver to: {$order->name}, {$order->address}, {$order->city}";

            return redirect()->route('orders.index')
                ->with('success', 'Order #'.$order->order_number.' placed! Opening WhatsApp to confirm with our bakers…')
                ->with('whatsapp_url', wa_link($message));
        }

        $message = setting('online_payment_message', 'Order #{order} placed! We will share a secure UPI / card payment link on WhatsApp shortly.');

        return redirect()->route('orders.index')->with('success', str_replace('{order}', $order->order_number, $message));
    }
}
