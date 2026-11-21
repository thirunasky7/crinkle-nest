<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Sign In · {{ setting('site_name', 'Crinkle Nest') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    @include('partials.tailwind-config')
</head>
<body class="min-h-screen bg-surface flex items-center justify-center p-4 font-body-md">
<div class="absolute -top-32 -left-20 w-96 h-96 rounded-full bg-secondary-fixed/40 blur-3xl pointer-events-none"></div>
<div class="relative w-full max-w-md bg-surface-container-low rounded-3xl p-8 sm:p-10 shadow-[0_8px_30px_rgba(61,35,20,0.08)]">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-11 h-11 rounded-full bg-primary text-on-primary flex items-center justify-center"><span class="material-symbols-outlined">cookie</span></div>
        <div>
            <div class="font-headline-sm text-headline-sm text-primary leading-none">{{ setting('site_name', 'Crinkle Nest') }}</div>
            <div class="font-label-sm text-label-sm text-secondary uppercase tracking-widest mt-1">Admin Panel</div>
        </div>
    </div>
    @if($errors->any())<div class="mb-4 p-3 rounded-xl bg-error-container text-on-error-container text-sm">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('admin.login') }}" class="flex flex-col gap-4">
        @csrf
        <div><label class="font-label-lg text-label-lg text-primary block mb-1.5" for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-3.5 rounded-xl bg-surface focus:outline-none focus:ring-2 focus:ring-secondary/40"></div>
        <div><label class="font-label-lg text-label-lg text-primary block mb-1.5" for="password">Password</label><input id="password" name="password" type="password" required class="w-full px-4 py-3.5 rounded-xl bg-surface focus:outline-none focus:ring-2 focus:ring-secondary/40"></div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="accent-secondary"> Remember me</label>
        <button class="w-full py-3.5 rounded-full bg-primary hover:bg-secondary text-on-primary font-label-lg text-label-lg transition-all">Sign In</button>
    </form>
</div>
</body>
</html>
