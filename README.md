# Website Pengelolaan Pemesanan dan Status Cucian pada Usaha Laundry Kiloan

Proyek UAS Pemrograman Web (individu) dan bahan Kerja Praktik (KP), Program Studi Teknik Informatika, Fakultas Teknik, Universitas Tanjungpura.

**Identitas Pengembang**
- **Nama:** Ninda Aliyya Ardiyanti
- **NIM:** D1041241007

## 1. Tautan

| Keterangan | Tautan |
|---|---|
| Repository GitHub | https://github.com/nindaaliyya-cyber/website-laundry |
| Laporan KP (Google Drive) | ISI_LINK_DRIVE |
| Website (setelah deployment) | ISI_ALAMAT_WEBSITE_JIKA_SUDAH_ADA |

## 2. Deskripsi Proyek

Website ini dikembangkan untuk membantu pengelolaan pemesanan dan pemantauan status cucian pada usaha laundry kiloan. Sistem memiliki tiga jenis pengguna, yaitu admin, karyawan, dan pelanggan.

Pelanggan dapat membuat pesanan dan memantau status cucian. Karyawan dapat melihat pesanan serta memperbarui status pengerjaan. Admin bertugas mengelola layanan, tarif, akun karyawan, dan seluruh pesanan.

Biaya laundry dihitung secara otomatis berdasarkan berat akhir cucian dikalikan tarif layanan yang dipilih.

## 3. Peran dan Hak Akses Pengguna

| Peran | Hak Akses |
|---|---|
| Admin | Mengelola layanan dan tarif, mengelola akun karyawan, melihat seluruh pesanan, serta memperbarui berat akhir dan status pesanan. |
| Karyawan | Melihat seluruh pesanan serta memperbarui berat akhir dan status pengerjaan. |
| Pelanggan | Mendaftarkan akun, membuat pesanan dengan satu atau beberapa layanan beserta isi cucian, dan melihat pesanan miliknya sendiri. |

**Catatan:** Akun admin dan karyawan contoh dibuat melalui `database.sql`. Kata sandi akun contoh harus diganti sebelum aplikasi digunakan pada lingkungan hosting.

## 4. Progres Tahap 1

| No. | Tanggal | Milestone | Catatan dan Perkembangan |
|---|---|---|---|
| 1 | ISI_TANGGAL | Perencanaan dan Analisis | Judul dan proposal KP ditetapkan. Analisis kebutuhan tiga jenis pengguna, rancangan basis data, serta rancangan hak akses telah disusun. |
| 2 | ISI_TANGGAL | Implementasi Awal | Autentikasi, CRUD layanan, pengelolaan akun karyawan, pemesanan, pengelolaan pesanan, dan dashboard telah dikembangkan dan dijalankan melalui Laragon. Perubahan rancangan meliputi penambahan peran karyawan dan dukungan beberapa layanan dalam satu pesanan. |

### Fitur yang Telah Dikembangkan

- [x] Registrasi, login, dan logout menggunakan session.
- [x] Tiga jenis pengguna dengan pembatasan hak akses di sisi server.
- [x] Pengelolaan layanan dan tarif (admin).
- [x] Pengelolaan akun karyawan (admin).
- [x] Pemesanan dengan satu atau beberapa layanan, isi cucian, dan keterangan pakaian khusus.
- [x] Pembaruan status dan berat akhir cucian.
- [x] Perhitungan biaya laundry secara otomatis.
- [x] Dashboard dengan informasi sesuai peran pengguna.

### Rencana Tahap 2

- Melakukan pengujian black box dan mendokumentasikan hasil pengujian.
- Melakukan deployment aplikasi ke layanan hosting.
- Mengevaluasi dan menyesuaikan sistem berdasarkan simulasi proses bisnis laundry serta referensi yang relevan.
- Menyusun bab hasil dan kesimpulan laporan.

## 5. Teknologi yang Digunakan

- **Bahasa pemrograman:** PHP
- **Basis data:** MySQL
- **Tampilan:** HTML dan CSS
- **JavaScript:** Interaksi pada halaman website
- **Lingkungan pengembangan:** Laragon
- **Version Control:** Git dan GitHub

## 6. Cara Menjalankan Aplikasi (Laragon)

1. Salin folder proyek ke direktori `C:\laragon\www\`.
2. Jalankan Apache dan MySQL melalui Laragon.
3. Impor file `database.sql` melalui HeidiSQL atau phpMyAdmin.
4. Salin file `koneksi.example.php` menjadi `koneksi.php`.
5. Sesuaikan konfigurasi koneksi basis data pada `koneksi.php`, termasuk kata sandi MySQL jika diperlukan.
6. Buka website melalui browser dengan alamat:

   `http://localhost/NAMA_FOLDER/`

   Ganti `NAMA_FOLDER` dengan nama folder proyek yang digunakan di dalam `C:\laragon\www\`.

## 7. Struktur Berkas Utama

| Berkas | Fungsi |
|---|---|
| `database.sql` | Berisi struktur basis data dan data awal. |
| `koneksi.example.php` | Contoh konfigurasi koneksi basis data dan fungsi pemeriksaan login serta hak akses. |
| `header.php`, `footer.php`, `style.css` | Mengatur tampilan, bagian halaman, dan menu berdasarkan peran pengguna. |
| `register.php`, `login.php`, `logout.php` | Menangani proses registrasi, login, dan logout. |
| `dashboard.php` | Menampilkan ringkasan informasi sesuai peran pengguna. |
| `layanan.php`, `pengguna.php` | Mengelola layanan, tarif, dan akun karyawan oleh admin. |
| `pesanan_buat.php`, `pesanan_saya.php` | Menangani pembuatan dan penampilan pesanan pelanggan. |
| `pesanan_admin.php` | Menangani pengelolaan pesanan oleh admin dan karyawan. |

## 8. Keamanan Aplikasi

Beberapa mekanisme keamanan yang diterapkan dalam pengembangan aplikasi meliputi:

- Kata sandi pengguna disimpan menggunakan fungsi `password_hash()`.
- Query basis data menggunakan prepared statement.
- Hak akses pengguna diperiksa di sisi server pada setiap halaman yang memerlukan pembatasan akses.
- Keluaran data diamankan menggunakan `htmlspecialchars()` untuk membantu mencegah serangan XSS.
- File konfigurasi `koneksi.php` tidak disertakan dalam repository GitHub karena dapat berisi informasi koneksi basis data.

---

**Program Studi Teknik Informatika**  
**Fakultas Teknik**  
**Universitas Tanjungpura**  
**2026**