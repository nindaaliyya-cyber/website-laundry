# Website Pengelolaan Pemesanan dan Status Cucian pada Usaha Laundry Kiloan

Proyek UAS Pemrograman Web (individu) dan bahan Kerja Praktik (KP), Program Studi Teknik Informatika, Fakultas Teknik, Universitas Tanjungpura.

**Identitas Pengembang**
- **Nama:** Ninda Aliyya Ardiyanti
- **NIM:** D1041241007

## 1. Tautan

| Keterangan | Tautan |
|---|---|
| Repository GitHub | https://github.com/nindaaliyya-cyber/website-laundry |
| Laporan KP (Google Drive) | Belum dicantumkan |
| Website hasil deployment | Belum tersedia / belum dicantumkan |

## 2. Deskripsi Proyek

Usaha laundry yang digunakan sebagai simulasi proses bisnis diberi nama **Laundry Ni-Ki**. Website ini dikembangkan untuk membantu pengelolaan pemesanan dan pemantauan status cucian. Sistem memiliki tiga jenis pengguna, yaitu admin, karyawan, dan pelanggan.

Pelanggan dapat membuat pesanan, melihat pesanan miliknya, membatalkan pesanan yang masih berstatus menunggu, dan membuka nota. Karyawan dapat melihat pesanan serta memperbarui berat akhir dan status pengerjaan. Admin dapat mengelola layanan, akun karyawan, dan seluruh pesanan.

Biaya dihitung berdasarkan berat akhir dikalikan tarif layanan. Jika satu kali proses pemesanan berisi beberapa layanan, aplikasi menyimpan setiap layanan sebagai baris pesanan tersendiri pada tabel `pesanan`; catatan umum disimpan pada baris pertama.

## 3. Peran dan Hak Akses

| Peran | Hak akses |
|---|---|
| Admin | Mengelola layanan dan tarif, mengelola akun karyawan, melihat/memperbarui/menghapus pesanan. |
| Karyawan | Melihat seluruh pesanan serta memperbarui berat akhir dan status pengerjaan. |
| Pelanggan | Registrasi, login, membuat pesanan, melihat pesanan sendiri, membatalkan pesanan berstatus menunggu, melihat dan mencetak nota miliknya, serta mengubah profil. |

## 4. Fitur yang Tersedia pada Source Code

- Registrasi pelanggan, login, dan logout berbasis session.
- Pembatasan halaman berdasarkan peran pengguna.
- Pengelolaan layanan dan tarif oleh admin.
- Pengelolaan akun karyawan oleh admin.
- Pemesanan dengan satu atau beberapa layanan, perkiraan berat, catatan, dan keterangan pakaian khusus.
- Pembaruan berat akhir, status, dan perhitungan biaya.
- Dashboard yang menampilkan informasi sesuai peran.
- Pembatalan pesanan oleh pelanggan selama status masih menunggu.
- Penghapusan pesanan oleh admin.
- Filter status dan pencarian nama pelanggan.
- Nota pesanan yang dapat dicetak melalui browser.
- Halaman profil untuk mengubah nama dan kata sandi.

## 5. Progres Pengerjaan

Tanggal dan status kegiatan perlu disesuaikan dengan pelaksanaan sebenarnya sebelum laporan dikumpulkan.

| Tahap | Kegiatan |
|---|---|
| Tahap 1 | Analisis kebutuhan, rancangan basis data dan hak akses, serta implementasi awal autentikasi, layanan, pengguna, pesanan, dan dashboard. |
| Tahap 2 | Penyempurnaan fitur, penambahan pengamanan formulir dan sesi, validasi berat akhir, serta pembaruan dokumentasi. |
| Belum diklaim selesai | Pengujian Black Box formal, pengujian seluruh skenario hak akses, dan deployment hosting harus dilakukan dan didokumentasikan terpisah. |

## 6. Teknologi

- PHP 8.x
- MySQL / MariaDB
- HTML, CSS, dan JavaScript
- Laragon untuk lingkungan pengembangan lokal
- Git dan GitHub untuk version control

## 7. Cara Menjalankan Aplikasi di Laragon

1. Pastikan PHP 8.x dan MySQL/MariaDB tersedia melalui Laragon.
2. Salin folder proyek ke `C:\laragon\www\`, misalnya `C:\laragon\www\website-laundry`.
3. Jalankan Apache dan MySQL melalui Laragon.
4. Buka phpMyAdmin atau HeidiSQL, lalu impor file `database.sql`. File tersebut membuat database `laundry_db`, tabel, layanan awal, serta akun contoh.
5. Periksa `koneksi.php`. Konfigurasi bawaan disiapkan untuk Laragon standar (`127.0.0.1`, pengguna `root`, kata sandi kosong, port `3306`). Sesuaikan jika konfigurasi MySQL berbeda.
6. Buka `http://localhost/website-laundry/` atau sesuaikan dengan nama folder proyek.

Jika `koneksi.php` belum ada pada salinan lain, salin `koneksi.example.php` menjadi `koneksi.php`, kemudian sesuaikan konfigurasi databasenya.

### Akun contoh

| Peran | Email | Kata sandi awal |
|---|---|---|
| Admin | admin@laundry.com | admin123 |
| Karyawan | karyawan@laundry.com | karyawan123 |

Ganti kata sandi akun contoh sebelum aplikasi digunakan pada server yang dapat diakses publik. Akun contoh hanya untuk instalasi lokal/pengujian.

## 8. Struktur Berkas Utama

| Berkas | Fungsi |
|---|---|
| `index.php` | Mengarahkan pengguna ke dashboard atau halaman login. |
| `database.sql` | Membuat database, tabel, layanan awal, dan akun contoh. |
| `koneksi.php` | Konfigurasi dan koneksi database lokal; tidak boleh berisi kredensial produksi di repository publik. |
| `koneksi.example.php` | Contoh konfigurasi koneksi database. |
| `fungsi.php` | Session, pemeriksaan login/hak akses, CSRF, escaping HTML, flash message, dan format rupiah. |
| `header.php`, `footer.php`, `style.css` | Kerangka halaman, navigasi, footer, dan tampilan. |
| `register.php`, `login.php`, `logout.php` | Registrasi, login, dan logout. |
| `dashboard.php` | Ringkasan informasi sesuai peran. |
| `layanan.php`, `pengguna.php` | Pengelolaan layanan dan akun karyawan. |
| `pesanan_buat.php`, `pesanan_saya.php` | Pembuatan dan daftar pesanan pelanggan. |
| `pesanan_admin.php` | Pengelolaan pesanan oleh admin dan karyawan. |
| `nota.php` | Menampilkan dan mencetak nota. |
| `profil.php` | Mengubah nama dan kata sandi akun. |

## 9. Pengamanan yang Diimplementasikan pada Source Code

- Kata sandi disimpan menggunakan `password_hash()` dan diverifikasi dengan `password_verify()`.
- Query yang menerima masukan pengguna menggunakan prepared statement.
- Keluaran teks di-escape menggunakan `htmlspecialchars()` melalui fungsi `e()`.
- Formulir POST menggunakan token CSRF.
- Session menggunakan strict mode, cookie `HttpOnly`, dan `SameSite=Lax`; atribut `Secure` aktif saat koneksi HTTPS.
- Login menerapkan pembatasan lima kegagalan selama 60 detik berdasarkan session browser. Ini bukan pembatasan global per alamat IP atau akun.
- Pelanggan hanya dapat membuka pesanan dan nota miliknya sendiri.
- Kata sandi baru minimal delapan karakter.

Keterangan di atas menjelaskan mekanisme yang ada pada source code; efektivitas dan kelengkapan keamanan tetap perlu diuji sebelum aplikasi dipublikasikan.

## 10. Pengujian dan Deployment

Hasil pengujian Black Box, pengujian hak akses, serta deployment belum dinyatakan berhasil hanya berdasarkan keberadaan source code. Catat skenario, hasil aktual, tanggal, dan bukti screenshot setelah pengujian langsung dilakukan. Jangan mencantumkan alamat hosting sampai website benar-benar berhasil diterapkan.

---

**Program Studi Teknik Informatika**  
**Fakultas Teknik**  
**Universitas Tanjungpura**  
**2026**
