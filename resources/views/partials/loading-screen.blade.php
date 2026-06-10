{{--
    Loading Screen Overlay
    Muncul saat halaman mulai dimuat, hilang otomatis setelah window.load.
    Dipasang sekali di setiap layout — tidak perlu duplikasi.
--}}
@vite('resources/js/loading.js')
<div id="loading-screen"
     style="position:fixed;inset:0;z-index:99999;
            background:rgba(17,17,17,0.45);
            backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);
            display:flex;align-items:center;justify-content:center;
            transition:opacity 0.3s ease;pointer-events:none;">
    <div id="lottie-loading" style="width:120px;height:120px;"></div>
</div>
