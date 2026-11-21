@extends('admin.layout')

@section('title', 'Message from '.$message->name)

@section('content')
<a href="{{ route('admin.messages.index') }}" class="adm-btn adm-btn-soft mb-5"><span class="material-symbols-outlined">arrow_back</span>Messages</a>
<div class="adm-card max-w-3xl">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-5">
        <div><div class="adm-label">Name</div>{{ $message->name }}</div>
        <div><div class="adm-label">Phone / WhatsApp</div>{{ $message->phone }}</div>
        <div><div class="adm-label">Email</div>{{ $message->email ?: '—' }}</div>
        <div><div class="adm-label">Inquiry type</div>{{ $message->inquiry_type ?: '—' }}</div>
        <div><div class="adm-label">Event / pickup date</div>{{ optional($message->event_date)->format('d M Y') ?: '—' }}</div>
        <div><div class="adm-label">Guests / boxes</div>{{ $message->guests ?: '—' }}</div>
        <div><div class="adm-label">WhatsApp copy requested</div>{{ $message->whatsapp_copy ? 'Yes' : 'No' }}</div>
        <div><div class="adm-label">Received</div>{{ $message->created_at->format('d M Y, g:i A') }}</div>
    </div>
    <div class="adm-label">Message</div>
    <div class="p-4 rounded-xl bg-surface-container-low whitespace-pre-line text-sm">{{ $message->message }}</div>
    <div class="flex flex-wrap gap-2 mt-5">
        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $message->phone) }}?text={{ rawurlencode('Hi '.$message->name.', thank you for reaching out to '.setting('site_name', 'Crinkle Nest').'! ') }}" target="_blank" rel="noopener" class="adm-btn bg-whatsapp text-white hover:bg-whatsapp-dark"><span class="material-symbols-outlined">chat</span>Reply on WhatsApp</a>
        @if($message->email)<a href="mailto:{{ $message->email }}" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">mail</span>Reply by email</a>@endif
        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">@csrf @method('DELETE')<button class="adm-btn adm-btn-danger"><span class="material-symbols-outlined">delete</span>Delete</button></form>
    </div>
</div>
@endsection
