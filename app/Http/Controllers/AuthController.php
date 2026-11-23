<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        seo()->page('login')->noindex();
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'These credentials do not match our records.']);
        }
        $guestOrders = session('guest_orders', []);
        $request->session()->regenerate();
        $this->claimGuestOrders($guestOrders);

        return redirect()->intended(route('profile.index'));
    }

    public function showRegister()
    {
        seo()->page('register')->noindex();
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'last_name' => 'nullable|string|max:80',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:8|confirmed',
        ]);
        unset($data['password_confirmation']);
        $data['points'] = (int) setting('signup_bonus_points', 0);
        $user = User::create($data);
        track_event('sign_up', ['method' => 'website']);
        $guestOrders = session('guest_orders', []);
        Auth::login($user);
        $request->session()->regenerate();
        $this->claimGuestOrders($guestOrders);

        return redirect()->route('profile.index')->with('success', 'Welcome to '.setting('site_name', 'Crinkle Nest').'!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /** Attach orders placed as a guest in this browser to the signed-in account. */
    protected function claimGuestOrders(array $ids): void
    {
        if ($ids && Auth::check()) {
            Order::whereIn('id', $ids)->whereNull('user_id')->update(['user_id' => Auth::id()]);
        }
    }
}
