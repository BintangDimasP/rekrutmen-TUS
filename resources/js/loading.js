import lottie from 'lottie-web';

(function () {
    var container = document.getElementById('lottie-loading');
    var screen    = document.getElementById('loading-screen');
    if (!container || !screen) return;

    var safetyTimer = null;
    var showDelayTimer = null;

    // Inisialisasi Lottie
    var anim = lottie.loadAnimation({
        container: container,
        renderer: 'svg',
        loop: true,
        autoplay: false,
        path: '/animations/loading.json',
    });

    function showLoader() {
        // Tunda kemunculan: kalau halaman selesai/berpindah dalam <500ms,
        // loader tidak pernah tampil sehingga tidak "ngeflash" di navigasi cepat.
        if (showDelayTimer) clearTimeout(showDelayTimer);
        showDelayTimer = setTimeout(renderLoader, 500);
    }

    function renderLoader() {
        screen.style.display = 'flex';
        screen.style.opacity = '1';
        screen.style.pointerEvents = 'none'; // tidak halangi klik
        anim.play();
        // Safety net: jika tidak terjadi navigasi (mis. unduh file),
        // sembunyikan otomatis agar loader tidak nyangkut selamanya.
        if (safetyTimer) clearTimeout(safetyTimer);
        safetyTimer = setTimeout(hideLoader, 4000);
    }

    function hideLoader() {
        if (showDelayTimer) { clearTimeout(showDelayTimer); showDelayTimer = null; }
        if (safetyTimer) { clearTimeout(safetyTimer); safetyTimer = null; }
        screen.style.opacity = '0';
        setTimeout(function () {
            screen.style.display = 'none';
            anim.stop();
        }, 300);
    }

    // Sembunyikan saat first load selesai (tidak tampil saat pertama buka)
    if (document.readyState === 'complete') {
        hideLoader();
    } else {
        window.addEventListener('load', hideLoader);
        setTimeout(hideLoader, 6000); // fallback
    }

    // Sembunyikan saat halaman dipulihkan dari bfcache (tombol back/forward)
    window.addEventListener('pageshow', hideLoader);

    // Tampilkan saat klik link internal (navigasi antar halaman)
    document.addEventListener('click', function (e) {
        var anchor = e.target.closest('a[href]');
        if (!anchor) return;
        var href = anchor.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript')) return;

        // Aksi unduh / buka tab baru (export, cetak berita acara, cetak individu):
        // tidak terjadi navigasi di tab ini. Biarkan browser menangani
        // (unduhan muncul di bilah download; tab baru punya loading bawaan
        // pada judul/favicon tab). Jadi TIDAK perlu loader sama sekali di sini.
        if (anchor.hasAttribute('download')
            || anchor.hasAttribute('data-no-loading')
            || anchor.target === '_blank') return;

        try {
            var url = new URL(href, window.location.href);
            if (url.origin !== window.location.origin) return;
        } catch (_) { return; }

        showLoader();
    });

    // Tampilkan saat submit form (skip form dengan data-no-loading)
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.hasAttribute('data-no-loading')) return;
        showLoader();
    });
})();
