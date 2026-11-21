<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* ----------------------------- Storefront ----------------------------- */

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactStore'])->middleware('throttle:10,1')->name('contact.store');
Route::get('/menu', [PageController::class, 'menu'])->name('menu');
Route::get('/menu/{slug}', [PageController::class, 'menu'])->name('menu.category');
Route::get('/page/{slug}', [PageController::class, 'policy'])->name('page');
Route::redirect('/terms', '/page/terms-and-conditions');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::post('/checkout', [CartController::class, 'checkout'])->middleware('throttle:10,1')->name('checkout');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{number}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
Route::post('/orders/{number}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
Route::post('/orders/{number}/rate', [OrderController::class, 'rate'])->name('orders.rate');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
    Route::put('/profile/preferences', [ProfileController::class, 'preferences'])->name('profile.preferences');
    Route::put('/profile/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
    Route::post('/profile/addresses', [ProfileController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/profile/addresses/{address}', [ProfileController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/profile/addresses/{address}', [ProfileController::class, 'destroyAddress'])->name('addresses.destroy');
    Route::post('/profile/addresses/{address}/default', [ProfileController::class, 'defaultAddress'])->name('addresses.default');
});

/* ------------------------------- Admin -------------------------------- */

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'saveSettings'])->name('settings.save');
        Route::get('/account', [AdminController::class, 'account'])->name('account');
        Route::post('/account', [AdminController::class, 'saveAccount'])->name('account.save');

        Route::get('/orders', [OrderAdminController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderAdminController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}', [OrderAdminController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderAdminController::class, 'destroy'])->name('orders.destroy');

        Route::get('/messages', [OrderAdminController::class, 'messages'])->name('messages.index');
        Route::get('/messages/{message}', [OrderAdminController::class, 'message'])->name('messages.show');
        Route::delete('/messages/{message}', [OrderAdminController::class, 'destroyMessage'])->name('messages.destroy');

        Route::get('/{resource}', [ResourceController::class, 'index'])->name('resource.index');
        Route::get('/{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
        Route::post('/{resource}', [ResourceController::class, 'store'])->name('resource.store');
        Route::get('/{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('resource.edit');
        Route::put('/{resource}/{id}', [ResourceController::class, 'update'])->name('resource.update');
        Route::delete('/{resource}/{id}', [ResourceController::class, 'destroy'])->name('resource.destroy');
    });
});
