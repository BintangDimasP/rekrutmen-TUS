import lottie from 'lottie-web';

(function () {
    var container = document.getElementById('lottie-loading');
    var screen    = document.getElementById('loading-screen');
    if (!container || !screen) return;

    var safetyTimer = null;
    var showDelayTimer = null;
    var topbar      = document.getElementById('top-progress');
    var topbarTimer = null;

    // Inisialisasi Lottie
    var anim = lottie.loadAnimation({
        container: container,
        renderer: 'svg',
        loop: true,
        autoplay: false,
        path: '/animations/loading.json',
    });

    function showLoader() {
        // Tunda kemunculan: kalau halaman selesai/berpindah dalam <350ms,
        // loader tidak pernah tampil sehingga tidak "ngeflash" di navigasi cepat.
        if (showDelayTimer) clearTimeout(showDelayTimer);
        showDelayTimer = setTimeout(renderLoader, 350);
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

    // ── Top progress bar (penanda untuk export/cetak) ──────────────────────
    function startTopBar() {
        if (!topbar) return;
        if (topbarTimer) clearTimeout(topbarTimer);
        topbar.style.transition = 'none';
        topbar.style.width = '0%';
        topbar.style.opacity = '1';
        void topbar.offsetWidth; // paksa reflow
        topbar.style.transition = 'width 1.8s cubic-bezier(0.1, 0.7, 0.3, 1)';
        topbar.style.width = '88%';
        // Aksi unduh/cetak tidak memicu navigasi, jadi selesaikan otomatis.
        topbarTimer = setTimeout(finishTopBar, 2000);
    }

    function finishTopBar() {
        if (!topbar) return;
        if (topbarTimer) { clearTimeout(topbarTimer); topbarTimer = null; }
        topbar.style.transition = 'width 0.25s ease';
        topbar.style.width = '100%';
        setTimeout(function () {
            topbar.style.transition = 'opacity 0.3s ease';
            topbar.style.opacity = '0';
            setTimeout(function () {
                topbar.style.transition = 'none';
                topbar.style.width = '0%';
            }, 320);
        }, 280);
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
        try {
            var url = new URL(href, window.location.href);
            if (url.origin !== window.location.origin) return;
        } catch (_) { return; }

        // Aksi export/cetak: hanya mengunduh file atau membuka tab baru —
        // tidak terjadi navigasi di tab ini. Tampilkan progress bar tipis
        // di atas (bukan gif layar penuh yang bisa nyangkut).
        var isUnduh  = anchor.hasAttribute('download') || anchor.hasAttribute('data-no-loading');
        var isTabBaru = anchor.target === '_blank';
        if (isUnduh || isTabBaru) {
            startTopBar();
            return;
        }

        showLoader();
    });

    // Tampilkan saat submit form (skip form dengan data-no-loading)
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.hasAttribute('data-no-loading')) {
            startTopBar();
            return;
        }
        showLoader();
    });
})();
