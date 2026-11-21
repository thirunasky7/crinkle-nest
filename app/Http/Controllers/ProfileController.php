<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('addresses');

        return view('pages.profile', [
            'stats' => [
                'orders' => $user->orders()->count(),
                'saved' => OrderItem::whereIn('order_id', $user->orders()->pluck('id'))->distinct('product_id')->count('product_id'),
            ],
            'vouchers' => Coupon::where('is_active', true)->where('show_in_profile', true)
                ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString()))->get(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'last_name' => 'nullable|string|max:80',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'location_label' => 'nullable|string|max:120',
            'birthday' => 'nullable|string|max:40',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        unset($data['password_confirmation']);
        $user->update($data);

        return back()->with('success', 'Your details were saved.');
    }

    public function avatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|max:4096']);
        $user = $request->user();
        if ($user->avatar && ! str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->update(['avatar' => $request->file('avatar')->store('avatars', 'public')]);

        return back()->with('success', 'Profile photo updated.');
    }

    public function preferences(Request $request)
    {
        $user = $request->user();
        $prefs = $user->preferences ?? [];
        $prefs['dietary'] = array_map('intval', (array) $request->input('dietary', []));
        $prefs['sweetness'] = (int) $request->input('sweetness', $prefs['sweetness'] ?? 2);
        if ($request->filled('packaging')) {
            $prefs['packaging'] = (int) $request->input('packaging');
        }
        $user->update(['preferences' => $prefs]);

        return back()->with('success', 'Baking preferences saved.');
    }

    public function notifications(Request $request)
    {
        $user = $request->user();
        $prefs = $user->preferences ?? [];
        $prefs['notifications'] = array_values(array_filter(array_map('intval', (array) $request->input('notifications', []))));
        $user->update(['preferences' => $prefs]);

        return back()->with('success', 'Notification preferences updated.');
    }

    protected function addressData(Request $request): array
    {
        return $request->validate([
            'label' => 'required|string|max:60',
            'icon' => 'nullable|string|max:40',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'recipient' => 'nullable|string|max:120',
            'phone' => 'nullable|string|max:30',
            'note' => 'nullable|string|max:255',
        ]) + ['is_default' => $request->boolean('is_default')];
    }

    public function storeAddress(Request $request)
    {
        $user = $request->user();
        $data = $this->addressData($request);
        if ($data['is_default'] || ! $user->addresses()->exists()) {
            $user->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }
        $user->addresses()->create($data);

        return back()->with('success', 'Address saved.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $data = $this->addressData($request);
        if ($data['is_default']) {
            $request->user()->addresses()->update(['is_default' => false]);
        }
        $address->update($data);

        return back()->with('success', 'Address updated.');
    }

    public function destroyAddress(Request $request, Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $wasDefault = $address->is_default;
        $address->delete();
        if ($wasDefault && ($next = $request->user()->addresses()->first())) {
            $next->update(['is_default' => true]);
        }

        return back()->with('success', 'Address removed.');
    }

    public function defaultAddress(Request $request, Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default address updated.');
    }
}
