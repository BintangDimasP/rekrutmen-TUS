<p align="center">
  <img src="public/images/logo.png" width="300" alt="Logo Rekrutmen TUS" onerror="this.src='https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg'">
</p>

# Sistem Informasi Rekrutmen Dosen & Tenaga Kependidikan

Sistem Informasi Rekrutmen adalah aplikasi berbasis web yang dirancang khusus untuk mengelola alur pendaftaran, penjadwalan, hingga proses penilaian seleksi calon pegawai (Dosen dan Tenaga Kependidikan). Sistem ini dibangun menggunakan **Laravel** dan **Tailwind CSS**, dengan interaktivitas yang didukung oleh **Alpine.js**.

## 🚀 Fitur Utama

Sistem ini mendukung *Multi-Role Authentication* dengan hak akses dan fitur spesifik untuk masing-masing pengguna:

### 1. Pelamar (Applicant)
- **Melihat Lowongan:** Melihat daftar lowongan pekerjaan yang sedang dibuka.
- **Melamar Pekerjaan:** Mengisi formulir pendaftaran, melengkapi berkas, dan memilih posisi/program studi tujuan.
- **Riwayat & Status Lamaran:** Memantau status lamaran (Menunggu, Lolos Administrasi, Penjadwalan, hingga Hasil Akhir).

### 2. Admin (HR / Kepegawaian)
- **Manajemen Lowongan & Pelamar:** Membuka/menutup lowongan dan melakukan verifikasi berkas administrasi pelamar.
- **Manajemen Penguji:** Menambah atau menetapkan dosen/staf yang akan bertugas sebagai penguji.
- **Penjadwalan Seleksi:** Mengatur jadwal sesi **Micro Teaching** dan **Wawancara** beserta plotting penguji untuk masing-masing sesi pelamar.
- **Rekapitulasi Nilai:** Memantau progres penilaian dari penguji dan melihat hasil rekapitulasi akhir dari seluruh pelamar.

### 3. Penguji (Reviewer / Interviewer)
- **Dashboard Jadwal:** Melihat daftar jadwal ujian pelamar yang ditugaskan kepada penguji tersebut.
- **Form Penilaian Terstruktur:** 
  - **Micro Teaching:** Menilai aspek Perencanaan Pembelajaran, Penggunaan Media, Sistematika, Pengelolaan Kelas, Sikap & Etika, serta Manajemen Waktu Pembelajaran.
  - **Wawancara:** Menilai aspek Motivasi, Potensi Kontribusi, Kemampuan Penelitian/Publikasi, Komunikasi, dan Relasi.
- **Rekomendasi Akhir:** Memberikan keputusan rekomendasi (Direkomendasikan, Dipertimbangkan, Tidak Direkomendasikan) beserta catatan khusus untuk Pelamar.

### 4. Kepala Program Studi (Kaprodi)
- **Review Hasil Penilaian:** Melihat detail nilai gabungan (Micro Teaching & Wawancara) dari pelamar yang ditugaskan ke program studinya.
- **Pertimbangan Akhir:** Kaprodi dapat meninjau hasil nilai dari para penguji sebagai bahan pertimbangan pengambilan keputusan kelulusan.

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** [Laravel](https://laravel.com/) (PHP Framework)
- **Frontend:** HTML, [Tailwind CSS](https://tailwindcss.com/) (Styling), [Alpine.js](https://alpinejs.dev/) (Interactivity)
- **Database:** MySQL
- **Alerts / Notifications:** SweetAlert2 & Toast (Custom Alpine Modal)

---

## ⚙️ Instalasi & Cara Menjalankan (Local Development)

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di komputer/laptop (Localhost):

1. **Clone Repository**
   ```bash
   git clone https://github.com/BintangDimasP/rekrutmen-TUS.git
   cd rekrutmen-TUS
   ```

2. **Install Dependencies**
   Install dependensi PHP menggunakan Composer dan dependensi Node.js menggunakan NPM.
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   ```
   *Atur `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di dalam file `.env` sesuai dengan MySQL Anda.*

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Migrasi Database & Seeder (Opsional)**
   Jalankan migrasi untuk membuat tabel database. Jika terdapat seeder untuk data awal, jalankan juga perintah seed.
   ```bash
   php artisan migrate --seed
   ```

6. **Link Storage**
   Jalankan perintah ini agar file unggahan/berkas (seperti CV atau foto) dapat diakses publik.
   ```bash
   php artisan storage:link
   ```

7. **Jalankan Aplikasi**
   Buka 2 terminal secara bersamaan dan jalankan perintah berikut:
   
   **Terminal 1 (Backend - Laravel):**
   ```bash
   php artisan serve
   ```
   
   **Terminal 2 (Frontend - Vite/Tailwind):**
   ```bash
   npm run dev
   ```

8. **Akses Website**
   Buka browser Anda dan akses: `http://localhost:8000`

---

## 👨‍💻 Kontributor
- **Bintang Dimas P.** - *Lead Developer*

*(Silakan tambahkan informasi lisensi atau catatan tambahan lainnya di bagian bawah README ini jika diperlukan).*
