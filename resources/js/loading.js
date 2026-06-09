import lottie from 'lottie-web';

(function () {
    var container = document.getElementById('lottie-loading');
    var screen    = document.getElementById('loading-screen');
    if (!container || !screen) return;

    // Inisialisasi Lottie
    var anim = lottie.loadAnimation({
        container: container,
        renderer: 'svg',
        loop: true,
        autoplay: false,
        path: '/animations/loading.json',
    });

    function showLoader() {
        screen.style.display = 'flex';
        screen.style.opacity = '1';
        screen.style.pointerEvents = 'none'; // tidak halangi klik
        anim.play();
    }

    function hideLoader() {
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

    // Tampilkan saat klik link internal (navigasi antar halaman)
    document.addEventListener('click', function (e) {
        var anchor = e.target.closest('a[href]');
        if (!anchor) return;
        var href = anchor.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript')
            || anchor.target === '_blank' || anchor.hasAttribute('download')) return;
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
