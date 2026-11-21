@extends('admin.layout')

@section('title', 'My Account')

@section('content')
<form method="POST" action="{{ route('admin.account.save') }}" class="adm-card max-w-xl flex flex-col gap-4">
    @csrf
    <div><label class="adm-label">Name</label><input name="name" value="{{ old('name', auth()->user()->name) }}" class="adm-input" required></div>
    <div><label class="adm-label">Email</label><input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="adm-input" required></div>
    <hr class="border-surface-container">
    <div><label class="adm-label">Current password</label><input type="password" name="current_password" class="adm-input" autocomplete="current-password"><p class="adm-help">Only needed when changing your password.</p></div>
    <div class="grid grid-cols-2 gap-3">
        <div><label class="adm-label">New password</label><input type="password" name="password" class="adm-input" autocomplete="new-password"></div>
        <div><label class="adm-label">Confirm</label><input type="password" name="password_confirmation" class="adm-input" autocomplete="new-password"></div>
    </div>
    <button class="adm-btn adm-btn-primary self-start"><span class="material-symbols-outlined">save</span>Save account</button>
</form>
@endsection
