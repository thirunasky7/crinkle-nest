<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /* ---------- Auth ---------- */

    public function showLogin()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Invalid email or password.']);
        }
        if (! Auth::user()->is_admin) {
            Auth::logout();

            return back()->withErrors(['email' => 'This account does not have admin access.']);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /* ---------- Dashboard ---------- */

    public function dashboard()
    {
        return view('admin.dashboard', [
            'stats' => [
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'active_orders' => Order::whereIn('status', Order::ACTIVE_STATUSES)->count(),
                'revenue_month' => Order::where('status', '!=', 'cancelled')->where('created_at', '>=', now()->startOfMonth())->sum('total'),
                'customers' => User::where('is_admin', false)->count(),
                'products' => Product::where('is_active', true)->count(),
                'unread' => ContactMessage::where('is_read', false)->count(),
            ],
            'sources' => Order::where('created_at', '>=', now()->subDays(30))->where('status', '!=', 'cancelled')
                ->selectRaw("COALESCE(utm_source, 'direct') as source, COUNT(*) as orders, SUM(total) as revenue")
                ->groupBy('source')->orderByDesc('revenue')->take(8)->get(),
            'subscriberCount' => \App\Models\Subscriber::where('is_active', true)->count(),
            'newSubscribers' => \App\Models\Subscriber::where('created_at', '>=', now()->subDays(30))->count(),
            'recentOrders' => Order::latest()->take(8)->get(),
            'recentMessages' => ContactMessage::latest()->take(5)->get(),
        ]);
    }

    /* ---------- Settings ---------- */

    public function settings(Request $request)
    {
        $group = $request->query('group', 'general');
        $settings = Setting::where('group', $group)->orderBy('sort')->get();

        return view('admin.settings', compact('group', 'settings'));
    }

    public function saveSettings(Request $request)
    {
        $group = $request->input('group', 'general');
        foreach (Setting::where('group', $group)->get() as $s) {
            if (in_array($s->type, ['image', 'file'], true)) {
                if ($file = $request->file('files.'.$s->key)) {
                    $request->validate(['files.'.$s->key => 'file|max:8192|mimes:jpg,jpeg,png,gif,webp,svg,ico,avif']);
                    if ($s->value && ! preg_match('~^https?://~', $s->value)) {
                        Storage::disk('public')->delete($s->value);
                    }
                    $s->value = $file->store('settings', 'public');
                } elseif ($request->boolean('remove.'.$s->key)) {
                    $s->value = null;
                } elseif ($request->has('values.'.$s->key)) {
                    $s->value = $request->input('values.'.$s->key);
                }
            } elseif ($s->type === 'boolean') {
                $s->value = $request->boolean('values.'.$s->key) ? '1' : '0';
            } elseif ($request->has('values.'.$s->key)) {
                $s->value = $request->input('values.'.$s->key);
            }
            $s->save();
        }
        Setting::flush();

        return redirect()->route('admin.settings', ['group' => $group])->with('success', 'Settings saved.');
    }

    /* ---------- Admin profile ---------- */

    public function account()
    {
        return view('admin.account');
    }

    public function saveAccount(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        return back()->with('success', 'Account updated.');
    }
}
