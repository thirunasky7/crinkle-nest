@if(session('success') || session('error') || $errors->any())
<div class="fixed top-24 inset-x-0 z-[60] flex justify-center px-4 pointer-events-none" data-dismiss-flash>
    <div class="pointer-events-auto max-w-lg w-full p-4 rounded-2xl shadow-[0_24px_48px_-12px_rgba(42,23,12,0.16)] flex items-start gap-3 {{ session('success') ? 'bg-emerald-50 text-emerald-900' : 'bg-error-container text-on-error-container' }}">
        <span class="material-symbols-outlined text-[22px]">{{ session('success') ? 'check_circle' : 'error' }}</span>
        <div class="font-body-md text-body-md">
            @if(session('success')) {{ session('success') }} @endif
            @if(session('error')) {{ session('error') }} @endif
            @if($errors->any())
                <ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            @endif
        </div>
        <button type="button" onclick="this.closest('[data-dismiss-flash]').remove()" class="ml-auto"><span class="material-symbols-outlined text-[18px]">close</span></button>
    </div>
</div>
@endif
