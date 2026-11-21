@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
@php $cls = 'w-full px-4 py-3.5 rounded-xl bg-surface text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-secondary/40 font-body-md text-body-md'; @endphp
<div class="relative w-full overflow-hidden">
    <div class="absolute -top-32 -right-20 w-96 h-96 rounded-full bg-tertiary-fixed/40 blur-3xl pointer-events-none -z-10"></div>
    <section class="max-w-lg mx-auto px-margin-mobile pt-12 pb-16">
        <div class="bg-surface-container-low rounded-3xl p-6 sm:p-10 shadow-[0_8px_30px_rgba(61,35,20,0.06)]">
            <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-semibold">Join the {{ setting('site_name', 'Crinkle Nest') }} family</span>
            <h1 class="font-headline-md text-headline-md text-primary mt-1 mb-6">Create your account</h1>
            <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2"><label class="font-label-lg text-label-lg text-primary" for="name">First Name *</label><input id="name" class="{{ $cls }}" name="name" value="{{ old('name') }}" required></div>
                    <div class="flex flex-col gap-2"><label class="font-label-lg text-label-lg text-primary" for="last_name">Last Name</label><input id="last_name" class="{{ $cls }}" name="last_name" value="{{ old('last_name') }}"></div>
                </div>
                <div class="flex flex-col gap-2"><label class="font-label-lg text-label-lg text-primary" for="email">Email *</label><input id="email" class="{{ $cls }}" type="email" name="email" value="{{ old('email') }}" required></div>
                <div class="flex flex-col gap-2"><label class="font-label-lg text-label-lg text-primary" for="phone">WhatsApp / Phone</label><input id="phone" class="{{ $cls }}" type="tel" name="phone" value="{{ old('phone') }}"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2"><label class="font-label-lg text-label-lg text-primary" for="password">Password *</label><input id="password" class="{{ $cls }}" type="password" name="password" required></div>
                    <div class="flex flex-col gap-2"><label class="font-label-lg text-label-lg text-primary" for="password_confirmation">Confirm *</label><input id="password_confirmation" class="{{ $cls }}" type="password" name="password_confirmation" required></div>
                </div>
                <button class="w-full py-4 px-6 rounded-full bg-primary hover:bg-secondary text-on-primary font-label-lg text-label-lg shadow-[0_4px_16px_rgba(37,15,3,0.25)] transition-all">Create Account</button>
            </form>
            <p class="font-body-md text-body-md text-on-surface-variant mt-6 text-center">Already have an account? <a class="text-secondary font-semibold hover:text-primary" href="{{ route('login') }}">Sign in</a></p>
        </div>
    </section>
</div>
@endsection
