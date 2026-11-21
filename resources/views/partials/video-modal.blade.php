<div id="videoModal" class="hidden fixed inset-0 z-[70] bg-black/80 backdrop-blur-sm items-center justify-center p-4" onclick="if(event.target===this) closeVideo()">
    <div class="relative w-full max-w-sm sm:max-w-md">
        <button type="button" onclick="closeVideo()" class="absolute -top-12 right-0 w-10 h-10 rounded-full bg-surface/90 text-primary flex items-center justify-center shadow-lg" aria-label="Close video">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div id="videoModalBody" class="rounded-2xl overflow-hidden bg-black aspect-[9/16] shadow-[0_24px_48px_-12px_rgba(42,23,12,0.5)]"></div>
        <p id="videoModalTitle" class="mt-3 text-center text-white font-title-md text-title-md"></p>
    </div>
</div>
@push('scripts')
<script>
    function openVideo(type, src, title) {
        if (type === 'link') { window.open(src, '_blank', 'noopener'); return; }
        const m = document.getElementById('videoModal');
        const b = document.getElementById('videoModalBody');
        b.innerHTML = type === 'file'
            ? '<video src="' + src + '" class="w-full h-full object-contain" controls autoplay playsinline></video>'
            : '<iframe src="' + src + '" class="w-full h-full" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';
        document.getElementById('videoModalTitle').textContent = title || '';
        m.classList.remove('hidden'); m.classList.add('flex');
    }
    function closeVideo() {
        const m = document.getElementById('videoModal');
        document.getElementById('videoModalBody').innerHTML = '';
        m.classList.add('hidden'); m.classList.remove('flex');
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeVideo(); });
</script>
@endpush
