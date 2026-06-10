{{--
    Loading Screen Overlay
    Muncul saat halaman mulai dimuat, hilang otomatis setelah window.load.
    Dipasang sekali di setiap layout — tidak perlu duplikasi.
--}}
@vite('resources/js/loading.js')
{{-- Progress bar tipis di atas halaman: penanda untuk aksi export/cetak (unduh file / buka tab baru) --}}
<div id="top-progress"
     style="position:fixed;top:0;left:0;height:3px;width:0;z-index:100000;
            background:linear-gradient(90deg,#8b1515,#c0392b);opacity:0;
            box-shadow:0 0 8px rgba(139,21,21,0.5);pointer-events:none;
            border-radius:0 2px 2px 0;"></div>
<div id="loading-screen"
     style="position:fixed;inset:0;z-index:99999;
            background:rgba(255,255,255,0.35);
            backdrop-filter:blur(3px);-webkit-backdrop-filter:blur(3px);
            display:flex;align-items:center;justify-content:center;
            transition:opacity 0.3s ease;pointer-events:none;">
    <div id="lottie-loading" style="width:120px;height:120px;"></div>
</div>
