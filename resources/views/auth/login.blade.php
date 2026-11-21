@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
@php $cls = 'w-full px-4 py-3.5 rounded-xl bg-surface text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-secondary/40 font-body-md text-body-md'; @endphp
<div class="relative w-full overflow-hidden">
    <div class="absolute -top-32 -left-20 w-96 h-96 rounded-full bg-secondary-fixed/40 blur-3xl pointer-events-none -z-10"></div>
    <section class="max-w-md mx-auto px-margin-mobile pt-12 pb-16">
        <div class="bg-surface-container-low rounded-3xl p-6 sm:p-10 shadow-[0_8px_30px_rgba(61,35,20,0.06)]">
            <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-semibold">{{ setting('site_name', 'Crinkle Nest') }} Account</span>
            <h1 class="font-headline-md text-headline-md text-primary mt-1 mb-6">Welcome back</h1>
            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                @csrf
                <div class="flex flex-col gap-2"><label class="font-label-lg text-label-lg text-primary" for="email">Email</label><input id="email" class="{{ $cls }}" type="email" name="email" value="{{ old('email') }}" required autofocus></div>
                <div class="flex flex-col gap-2"><label class="font-label-lg text-label-lg text-primary" for="password">Password</label><input id="password" class="{{ $cls }}" type="password" name="password" required></div>
                <label class="flex items-center gap-2 font-body-sm text-body-sm"><input type="checkbox" name="remember" class="accent-secondary"> Keep me signed in</label>
                <button class="w-full py-4 px-6 rounded-full bg-primary hover:bg-secondary text-on-primary font-label-lg text-label-lg shadow-[0_4px_16px_rgba(37,15,3,0.25)] transition-all">Sign In</button>
            </form>
            <p class="font-body-md text-body-md text-on-surface-variant mt-6 text-center">New here? <a class="text-secondary font-semibold hover:text-primary" href="{{ route('register') }}">Create an account</a></p>
        </div>
    </section>
</div>
@endsection
