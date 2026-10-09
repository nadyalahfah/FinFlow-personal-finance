# FinFlow — Aplikasi Pengelolaan Keuangan Pribadi

FinFlow adalah aplikasi berbasis web yang dirancang untuk membantu pengguna mengelola keuangan pribadi secara lebih mudah, teratur, dan efisien. Aplikasi ini memungkinkan pengguna mencatat pemasukan dan pengeluaran, mengelola beberapa dompet, melakukan transfer saldo, serta memantau kondisi keuangan melalui dashboard yang informatif.

FinFlow menyediakan ringkasan keuangan, riwayat transaksi, grafik keuangan bulanan, dan peringatan anggaran untuk membantu pengguna memahami kebiasaan pengeluaran dan mengelola keuangan dengan lebih baik.

## Fitur Utama

### 1. Autentikasi Pengguna

* Registrasi akun baru.
* Login dan logout pengguna.
* Pengelolaan sesi pengguna.
* Pembatasan akses dashboard bagi pengguna yang sudah login.

### 2. Dashboard Keuangan

* Menampilkan saldo dompet yang dimiliki pengguna.
* Menampilkan ringkasan pemasukan dan pengeluaran bulanan.
* Menyaring data keuangan berdasarkan bulan.
* Menampilkan riwayat transaksi terbaru.
* Memberikan peringatan ketika pengeluaran melebihi anggaran yang ditentukan.

### 3. Pengelolaan Transaksi

* Mencatat transaksi pemasukan dan pengeluaran.
* Menambahkan judul, nominal, kategori, dan tanggal transaksi.
* Memilih dompet yang digunakan dalam transaksi.
* Menampilkan riwayat transaksi dalam bentuk tabel.
* Memperbarui saldo dompet berdasarkan transaksi yang dicatat.

### 4. Pengelolaan Dompet

* Mengelola beberapa dompet keuangan.
* Melihat saldo setiap dompet.
* Melakukan transfer uang antar-dompet.
* Menyesuaikan saldo dompet asal dan tujuan setelah transfer.

### 5. Analisis Keuangan

* Menampilkan grafik pemasukan dan pengeluaran selama enam bulan terakhir.
* Membandingkan pemasukan dan pengeluaran setiap bulan.
* Menampilkan ringkasan keuangan bulanan.
* Membantu pengguna mengidentifikasi pola pengeluaran dan penggunaan anggaran.

### 6. Laporan Keuangan

* Menampilkan laporan berdasarkan bulan yang dipilih.
* Menyediakan halaman laporan yang dapat dicetak.
* Memudahkan pengguna meninjau dan mendokumentasikan kondisi keuangan.

### 7. Antarmuka Pengguna

* Tampilan dashboard yang interaktif.
* Dukungan tema terang dan gelap.
* Format mata uang Rupiah (IDR).
* Visualisasi data menggunakan Chart.js.

## Teknologi yang Digunakan

| Teknologi   | Kegunaan                                     |
| ----------- | -------------------------------------------- |
| PHP         | Mengelola logika aplikasi di sisi server     |
| MySQL       | Menyimpan dan mengelola data keuangan        |
| PDO         | Menghubungkan aplikasi dengan database       |
| HTML5       | Menyusun struktur halaman web                |
| CSS3        | Mengatur tampilan dan tata letak aplikasi    |
| JavaScript  | Membuat antarmuka lebih interaktif           |
| Chart.js    | Menampilkan grafik pemasukan dan pengeluaran |
| PHP Session | Mengelola sesi login pengguna                |

## Struktur Proyek

```text
FinFlow-personal-finance/
├── api.php             # Pemrosesan transaksi dan data dashboard
├── db.php              # Koneksi database dan pemeriksaan autentikasi
├── index.php           # Halaman utama dashboard keuangan
├── login.php           # Halaman login pengguna
├── logout.php          # Proses logout pengguna
├── print_report.php    # Halaman laporan keuangan untuk dicetak
├── register.php        # Halaman registrasi pengguna
├── style.css           # Pengaturan tampilan aplikasi
└── README.md           # Dokumentasi proyek
```

## Persyaratan Sistem

Sebelum menjalankan aplikasi, pastikan perangkat telah memiliki:

* PHP 8.0 atau versi yang lebih baru direkomendasikan.
* MySQL 8.0 atau MariaDB yang kompatibel.
* Apache atau server web yang mendukung PHP.
* Browser modern seperti Google Chrome atau Mozilla Firefox.
* XAMPP, WAMP, atau lingkungan pengembangan lokal sejenis.
* Ekstensi PHP PDO MySQL yang telah diaktifkan.

## Instalasi dan Konfigurasi

### 1. Clone Repository

Buka terminal atau Command Prompt, kemudian jalankan perintah berikut:

```bash
git clone https://github.com/nadyalahfah/FinFlow-personal-finance.git
```

Masuk ke direktori proyek:

```bash
cd FinFlow-personal-finance
```

### 2. Menyiapkan Database

Jalankan Apache dan MySQL melalui XAMPP, kemudian buka phpMyAdmin melalui alamat:

```text
http://localhost/phpmyadmin
```

Buat database dengan nama `finflow` menggunakan perintah SQL berikut:

```sql
CREATE DATABASE finflow;
```

Selanjutnya, siapkan struktur tabel dan data awal yang dibutuhkan oleh aplikasi. Pastikan tabel dan kolom yang dibuat sesuai dengan kebutuhan kode program, termasuk data pengguna, dompet, transaksi, dan kategori.

**Catatan:** Berkas ekspor database belum tercantum dalam struktur repository yang didokumentasikan. Oleh karena itu, struktur tabel perlu disiapkan secara terpisah sebelum aplikasi dijalankan.

### 3. Mengatur Koneksi Database

Buka file `db.php`, kemudian sesuaikan konfigurasi koneksi database dengan pengaturan MySQL di perangkatmu.

Contoh konfigurasi lokal:

```php
$host = 'localhost';
$dbname = 'finflow';
$username = 'root';
$password = '';
```

Konfigurasi tersebut merupakan contoh untuk lingkungan pengembangan lokal. Sesuaikan nama database, username, dan password apabila berbeda.

### 4. Menjalankan Aplikasi

Jika menggunakan XAMPP, ikuti langkah berikut:

1. Letakkan folder proyek di dalam direktori `htdocs`.
2. Jalankan Apache dan MySQL.
3. Pastikan database `finflow` beserta tabel yang dibutuhkan sudah tersedia.
4. Buka browser dan akses alamat berikut:

```text
http://localhost/FinFlow-personal-finance/
```

Sesuaikan alamat tersebut jika nama folder proyek berbeda.

### 5. Menggunakan Aplikasi

Setelah aplikasi berhasil dijalankan, buat akun melalui halaman registrasi, kemudian login untuk masuk ke dashboard.

Pengguna dapat mulai mengelola dompet, mencatat transaksi, memantau saldo, melihat grafik keuangan, serta mencetak laporan sesuai fitur yang tersedia.

## Panduan Penggunaan

1. **Registrasi dan Login:** Buat akun baru atau masuk menggunakan akun yang sudah terdaftar.
2. **Melihat Dashboard:** Periksa ringkasan pemasukan, pengeluaran, saldo, dan transaksi terbaru.
3. **Mencatat Transaksi:** Masukkan informasi transaksi, seperti jenis transaksi, judul, nominal, kategori, dompet, dan tanggal.
4. **Mengelola Dompet:** Pantau saldo masing-masing dompet dan lakukan transfer antar-dompet jika diperlukan.
5. **Memfilter Data:** Pilih bulan yang diinginkan untuk melihat ringkasan keuangan dan riwayat transaksi.
6. **Menganalisis Keuangan:** Gunakan grafik untuk membandingkan pemasukan dan pengeluaran selama enam bulan terakhir.
7. **Memantau Anggaran:** Perhatikan peringatan ketika pengeluaran melampaui batas anggaran.
8. **Mencetak Laporan:** Buka halaman laporan keuangan untuk mencetak ringkasan sesuai periode yang dipilih.

## Gambaran Database

Aplikasi FinFlow memanfaatkan database MySQL untuk menyimpan dan mengelola informasi keuangan pengguna.

Entitas data utama yang dirujuk dalam dokumentasi aplikasi meliputi:

* **Pengguna (Users):** Menyimpan informasi akun untuk autentikasi.
* **Dompet (Wallets):** Menyimpan informasi dompet dan saldo yang terkait dengan pengguna.
* **Transaksi (Transactions):** Menyimpan catatan pemasukan, pengeluaran, dan transfer.
* **Kategori (Categories):** Mengelompokkan transaksi dan mendukung pengelolaan anggaran sesuai implementasi aplikasi.

Data tersebut digunakan untuk menampilkan saldo, menghitung ringkasan keuangan, menyusun grafik, dan membantu pengguna memantau pengeluaran.
