# Product Information System - Nurmala Lubis

Mini Project 1 - Product Information System.

## Deskripsi

Product Information System merupakan sistem informasi berbasis web sederhana yang digunakan untuk menyimpan, mengolah, dan menampilkan data inventori produk. Sistem ini mengusung antarmuka bertema pink yang modern dan dipisahkan secara terstruktur berdasarkan Data Layer, Processing Layer, dan Presentation Layer.

## Fitur

* **Autentikasi**`: Login dan Logout sistem
* **Dashboard**: Ringkasan informasi & total nilai aset gudang
* **Katalog Produk**: Melihat daftar inventori lengkap beserta indikator status stok
* **Input Produk**: Menambahkan data produk baru ke dalam sistem
* **Otomatisasi Kalkulasi**: Menghitung total nilai stok produk secara otomatis
* **Peringatan Stok**: Indikator otomatis untuk produk dengan status stok kritis

## Data Produk

Setiap data produk mencakup atribut berikut:

* **ID / SKU**: Kode unik produk
* **Nama Produk**: Nama barang/item
* **Kategori**: Kelompok jenis produk
* **Harga**: Price per unit (Rp)
* **Stok**: Jumlah ketersediaan item
* **Deskripsi**: Penjelasan singkat produk

## Struktur Project

Berikut adalah susunan berkas dalam direktori project:

* `config.php` : Konfigurasi dasar aplikasi dan kredensial autentikasi
* `functions.php` : Berisi fungsi logika pengolahan data (Processing Layer)
* `index.php` : Halaman utama (Dashboard)
* `lihat_produk.php` : Halaman untuk menampilkan tabel daftar inventori produk
* `login.php` : Halaman antarmuka masuk ke dalam sistem
* `logout.php` : Skrip proses keluar dari sesi aplikasi
* `products.php` : Pengolah dan penghubung data katalog produk
* `products_data.json` : Berkas sumber penyimpanan data produk (Data Layer)
* `README.md` : Dokumentasi lengkap 
* `register.php` : Halaman formulir pendaftaran akun pengguna baru
* `tambah_produk.php` : Halaman form input data produk baru

## Teknologi

* **PHP** (Fundamental PHP & Session Management)
* **HTML5 & CSS3** (Custom Styling & Pink Theme)
* **Bootstrap 5** (Framework UI Responsive)
* **JSON** (Data Storage Format)

## Konsep Pengembangan

Project ini dibangun menggunakan pendekatan pemisahan tanggung jawab kode:

### Data Layer
Penyimpanan dan manajemen data produk ditangani oleh `products_data.json` dan diproses melalui `products.php`.

### Processing Layer
Seluruh logika bisnis seperti kalkulasi total aset stok (`hitungTotalNilaiStok()`) dan evaluasi kondisi stok berada di dalam `functions.php`.

### Presentation Layer
Seluruh tampilan antarmuka (UI) dikelola pada file PHP utama seperti `index.php`, `lihat_produk.php`, `tambah_produk.php`, dan `login.php`.

## Cara Menjalankan

### Akses Lokal (XAMPP)
1. Pastikan aplikasi web server seperti **XAMPP** sudah terpasang.
2. Jalankan modul **Apache** pada Control Panel XAMPP.
3. Salin folder project ini ke dalam direktori `C:/xampp/htdocs/product_information_system_nurmala_lubis`.
4. Buka browser dan akses alamat berikut:
   `http://localhost/product_information_system_nurmala_lubis/`
5. Masuk menggunakan akun admin yang telah dikonfigurasi di file `config.php`.

### Akses Live Demo (HTTPS Online)
1. Akses tautan berikut di browser HP/PC:
   `https://pis-nurmala-lubis.infinityfreeapp.com/`
2. **Catatan jika muncul peringatan "Google Safe Browsing / Deceptive Site":**
   * Klik **Sembunyikan Detail** (atau *Details*) di bagian bawah layar merah.
   * Klik tautan **buka situs yang tidak aman ini** (atau *proceed to this unsafe site*).
   * Website akan terbuka dan siap digunakan secara normal.