# White Box Testing - Dashboard Pengguna

## Deskripsi

Pengujian pada Dashboard Pengguna dilakukan untuk mengevaluasi logika tampilan data berdasarkan empat peran (_role_) yang berbeda: Admin, Pelamar, Penguji, dan Kaprodi. Implementasi ini dilakukan dengan merancang skenario yang mencakup kondisi valid, _error handling_, serta _edge cases_ untuk memastikan seluruh jalur logika tereksekusi setidaknya satu kali. Pengujian difokuskan pada empat _controller_: `Admin\DashboardController@index`, `Pelamar\DashboardController@index`, `Penguji\PengujiController@dashboard`, dan `Kaprodi\KaprodiController@dashboard`.

## Test Cases

| Test Case | Skenario | Hasil yang Diharapkan |
|-----------|----------|----------------------|
| TC-01 | Admin mengakses dashboard dengan data lengkap | Sistem menampilkan halaman dashboard admin dengan statistik total lowongan, pelamar, lamaran, distribusi status, dan grafik bulanan |
| TC-02 | Admin mengakses dashboard tanpa data | Sistem menampilkan halaman dashboard admin dengan seluruh statistik bernilai 0 dan grafik kosong |
| TC-03 | Admin mengakses dashboard dengan lamaran tanpa ada yang diterima | Sistem menampilkan halaman dashboard admin dengan grafik pelamar diterima bernilai 0 |
| TC-04 | Pelamar mengakses dashboard dengan riwayat lamaran | Sistem menampilkan halaman dashboard pelamar dengan total lamaran, status lamaran, dan riwayat lamaran terbaru |
| TC-05 | Pelamar pertama login dengan profil tidak lengkap | Sistem menampilkan halaman dashboard pelamar dengan modal peringatan kelengkapan profil |
| TC-06 | Pelamar pertama login dengan profil lengkap | Sistem menampilkan halaman dashboard pelamar tanpa modal peringatan |
| TC-07 | Penguji mengakses dashboard dengan jadwal pengujian | Sistem menampilkan halaman dashboard penguji dengan total diuji, selesai dinilai, belum dinilai, dan jadwal pengujian 7 hari ke depan |
| TC-08 | Penguji mengakses dashboard tanpa jadwal pengujian | Sistem menampilkan halaman dashboard penguji dengan seluruh statistik bernilai 0 dan daftar jadwal kosong |
| TC-09 | Kaprodi mengakses dashboard dengan data pelamar di prodinya | Sistem menampilkan halaman dashboard kaprodi dengan total pelamar, status lamaran, dan daftar lamaran terbaru |
| TC-10 | Kaprodi mengakses dashboard tanpa data pelamar di prodinya | Sistem menampilkan halaman dashboard kaprodi dengan seluruh statistik bernilai 0 dan daftar lamaran kosong |

## Hasil Pengujian

Berdasarkan skenario pengujian pada tabel tersebut, implementasi pengujian kemudian dijalankan menggunakan _tools_ PEST, yang menghasilkan keluaran sebagai berikut:

```
PASS  Tests\Feature\DashboardTest
✓ Dashboard Admin → TC-01: Admin mengakses dashboard dengan data lengkap, sistem menampilkan dashboard admin dengan statistik dan grafik
✓ Dashboard Admin → TC-02: Admin mengakses dashboard tanpa data, sistem menampilkan dashboard admin dengan statistik 0
✓ Dashboard Admin → TC-03: Admin mengakses dashboard dengan lamaran tanpa ada yang diterima, sistem menampilkan dashboard admin dengan grafik pelamar diterima 0
✓ Dashboard Pelamar → TC-04: Pelamar mengakses dashboard dengan riwayat lamaran, sistem menampilkan dashboard pelamar dengan status
✓ Dashboard Pelamar → TC-05: Pelamar pertama login dengan profil tidak lengkap, sistem menampilkan dashboard pelamar dengan modal peringatan profil
✓ Dashboard Pelamar → TC-06: Pelamar pertama login dengan profil lengkap, sistem menampilkan dashboard pelamar tanpa modal peringatan
✓ Dashboard Penguji → TC-07: Penguji mengakses dashboard dengan jadwal pengujian, sistem menampilkan dashboard penguji dengan statistik dan jadwal
✓ Dashboard Penguji → TC-08: Penguji mengakses dashboard tanpa jadwal pengujian, sistem menampilkan dashboard penguji dengan statistik 0
✓ Dashboard Kaprodi → TC-09: Kaprodi mengakses dashboard dengan data pelamar di prodinya, sistem menampilkan dashboard kaprodi dengan statistik dan daftar lamaran
✓ Dashboard Kaprodi → TC-10: Kaprodi mengakses dashboard tanpa data pelamar di prodinya, sistem menampilkan dashboard kaprodi dengan statistik 0

Tests:  10 passed (49 assertions)
```

Berdasarkan keluaran pengujian di atas, seluruh _test cases_ pada fitur Dashboard Pengguna berhasil memperoleh status _passed_. Hal ini membuktikan bahwa setiap cabang logika telah tereksekusi secara menyeluruh dan terbukti bebas dari cacat logika maupun _bug_ kritis. Dengan demikian, sistem terbukti mampu menangani berbagai kondisi skenario secara valid sesuai dengan kebutuhan fungsional yang ditetapkan.
