import { chromium } from 'playwright';

/**
 * AUTOMATED DEMO ALUR SISTEM (Hanya Menggunakan Data yang Sudah Ada)
 * 
 * Penggunaan:
 *   1. Jalankan untuk Production: node automated_demo.js https://domain-kamu.my.id
 *   2. Jalankan untuk Lokal:      node automated_demo.js http://127.0.0.1:8000
 */

const BASE_URL = process.argv[2] || 'http://127.0.0.1:8000';

async function runDemoExistingData() {
    console.log(`====================================================`);
    console.log(`🚀 MEMULAI DEMO OTOMATIS (DATA EKSISTING)`);
    console.log(`📍 Target URL: ${BASE_URL}`);
    console.log(`====================================================\n`);

    const browser = await chromium.launch({
        headless: false,
        slowMo: 1500, // Jeda 1.5 detik per aksi agar terlihat jelas saat demo
    });

    const context = await browser.newContext({ viewport: { width: 1366, height: 768 } });
    const page = await context.newPage();

    try {
        // 1. HALAMAN UTAMA (PUBLIC)
        console.log(`[1/4] 🌐 Menelusuri Beranda & Lowongan Terbuka...`);
        await page.goto(`${BASE_URL}/`, { waitUntil: 'networkidle' });
        await page.waitForTimeout(2000);
        await page.evaluate(() => window.scrollBy({ top: 500, behavior: 'smooth' }));
        await page.waitForTimeout(2000);

        // 2. DASHBOARD ADMIN & MANAJEMEN PELAMAR (A - Z)
        console.log(`[2/4] 🔑 Login Admin & Cek Data Pelamar (Urutan A-Z)...`);
        await page.goto(`${BASE_URL}/login`);
        await page.fill('#email', 'admin@admin.telkomuniversity.ac.id');
        await page.fill('#password', 'admin123456');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Buka Halaman Pelamar (Melihat daftar A-Z)
        await page.goto(`${BASE_URL}/admin/pelamars`);
        await page.evaluate(() => window.scrollBy({ top: 400, behavior: 'smooth' }));
        await page.waitForTimeout(3000);

        // Buka Detail Pelamar Pertama (jika ada)
        const detailBtn = page.locator('a:has-text("Detail"), a:has-text("Lihat")').first();
        if (await detailBtn.isVisible()) {
            console.log(`     📄 Membuka Detail Berkas Pelamar Eksisting...`);
            await detailBtn.click();
            await page.waitForLoadState('networkidle');
            await page.evaluate(() => window.scrollBy({ top: 400, behavior: 'smooth' }));
            await page.waitForTimeout(3000);
        }

        // 3. JADWAL SELEKSI & REKAPITULASI
        console.log(`[3/4] 📅 Meninjau Jadwal Seleksi & Rekapitulasi Nilai...`);
        await page.goto(`${BASE_URL}/admin/jadwals`);
        await page.waitForTimeout(2500);

        // 4. VERIFIKASI SELESAI & LOGOUT
        console.log(`[4/4] 🚪 Demo Selesai, Menutup Sesi...`);
        await page.goto(`${BASE_URL}/admin/dashboard`);
        await page.waitForTimeout(2000);

        console.log(`\n====================================================`);
        console.log(`🎉 DEMO DATA EKSISTING BERHASIL DILAKUKAN!`);
        console.log(`====================================================`);

    } catch (error) {
        console.error(`\n❌ Catatan saat demo:`, error.message);
    } finally {
        await page.waitForTimeout(2000);
        await browser.close();
    }
}

runDemoExistingData();
