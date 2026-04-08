# SIS Dashboard - Team Project Management System

## Deskripsi

SIS Dashboard adalah sistem manajemen proyek berbasis PHP native yang dikonversi dari HTML statis. Sistem ini memungkinkan pengelolaan proyek, tugas, isu, dan pelaporan tanpa memerlukan autentikasi atau login.
Untuk akses local buka di localhost/sis-dashboard

## Fitur

- **Manajemen Proyek**: Tambah, edit, dan lihat detail proyek
- **Manajemen Tugas**: Tugas instalasi dan programming dengan tracking progress
- **Troubleshooting**: Pelaporan dan penanganan isu/bug
- **Penjadwalan**: Manajemen jadwal dan timeline tugas
- **Pelaporan**: Progress report dan dokumentasi
- **Upload Dokumen**: Sistem upload file untuk dokumentasi

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL/MariaDB
- Web server (Apache, Nginx, atau XAMPP)
- Ekstensi PHP: mysqli, fileinfo

## Instalasi

### 1. Setup Database

1. Buat database baru dengan nama `team_project`
2. Import file `team_project.sql` ke database Anda
3. Pastikan semua tabel terbuat dengan benar

### 2. Konfigurasi Database

Edit file `config.php` sesuai dengan konfigurasi database Anda:

```php
$host = 'localhost';        // Ganti jika perlu
$username = 'root';        // Ganti dengan username database Anda
$password = '';            // Ganti dengan password database Anda
$database = 'team_project'; // Nama database
```

### 3. Setup Folder

1. Pastikan semua file PHP berada di folder yang sama
2. Buat folder `uploads/` dengan permission write (777) untuk upload file
3. Pastikan file `styles.css` ada di folder yang sama

### 4. Akses Aplikasi

Buka browser dan akses:
- URL utama: `http://localhost/[nama-folder]/index.php`

## Struktur File

```
project-folder/
├── config.php                 # Konfigurasi database
├── index.php                  # Halaman dashboard utama
├── all_projects.php           # Daftar semua proyek
├── project_add.php            # Tambah proyek baru
├── project_edit.php           # Edit proyek
├── project_details.php        # Detail proyek
├── installation.php           # Daftar tugas instalasi
├── installation_add.php        # Tambah tugas instalasi
├── installation_edit.php       # Edit tugas instalasi
├── installation_view.php       # Lihat detail tugas instalasi
├── programming.php            # Daftar tugas programming
├── programming_add.php         # Tambah tugas programming
├── programming_edit.php        # Edit tugas programming
├── programming_view.php        # Lihat detail tugas programming
├── troubleshooting.php        # Daftar isu/troubleshooting
├── troubleshooting_add.php     # Tambah isu baru
├── troubleshooting_edit.php    # Edit isu
├── troubleshooting_view.php    # Lihat detail isu
├── schedule.php               # Jadwal dan timeline
├── schedule_add.php            # Tambah jadwal
├── schedule_edit.php           # Edit jadwal
├── schedule_view.php           # Lihat detail jadwal
├── report_progress.php         # Progress report
├── report_view.php             # Lihat detail report
├── documentation_upload.php    # Upload dokumentasi
├── styles.css                  # File CSS styling
├── team_project.sql            # File SQL database
└── uploads/                    # Folder untuk file upload
```

## Database Schema

Database `team_project` memiliki tabel-tabel berikut:

- `projects`: Data proyek
- `tasks`: Data tugas (installation & programming)
- `issues`: Data isu/troubleshooting
- `reports`: Data laporan/progress report
- `attachments`: Data file upload
- `issue_notes`: Catatan untuk isu

## Penggunaan

### 1. Dashboard
- Lihat statistik proyek aktif, selesai, dan isu
- Akses cepat ke fitur tambah proyek
- Lihat progress overview proyek terbaru
- Lihat isu dan jadwal terbaru

### 2. Manajemen Proyek
- **Tambah Proyek**: Klik tombol "+" di dashboard atau halaman proyek
- **Edit Proyek**: Klik tombol "Edit Project" di detail proyek
- **Lihat Detail**: Klik pada kartu proyek untuk melihat detail lengkap

### 3. Manajemen Tugas
- **Installation**: Tugas terkait instalasi hardware/software
- **Programming**: Tugas terkait pengembangan software
- Setiap tugas memiliki status, progress, dan timeline

### 4. Troubleshooting
- Laporkan isu/bug yang ditemukan
- Tambahkan detail teknis, error code, dan log sistem
- Track priority dan impact level

### 5. Penjadwalan
- Lihat semua tugas dalam timeline
- Filter berdasarkan status dan jenis tugas
- Tambah jadwal baru untuk tugas

### 6. Pelaporan
- Buat progress report untuk proyek
- Upload dokumentasi terkait
- Lihat statistik dan summary proyek

## Konfigurasi Tambahan

### Upload File
- Folder `uploads/` harus writable
- Maksimal ukuran file: 10MB (dapat diubah di php.ini)
- Format yang didukung: PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, PNG, GIF

### Styling
- File `styles.css` mengandung semua styling
- Responsive design untuk mobile dan desktop
- Warna tema dapat disesuaikan di CSS

## Troubleshooting

### Error Koneksi Database
- Pastikan konfigurasi di `config.php` benar
- Cek service MySQL/MariaDB sudah running
- Verifikasi username dan password database

### Error Upload File
- Pastikan folder `uploads/` ada dan writable
- Cek php.ini untuk `upload_max_filesize` dan `post_max_size`
- Pastikan ekstensi `fileinfo` aktif

### Tampilan Tidak Berfungsi
- Pastikan file `styles.css` ada dan dapat diakses
- Cek error log untuk PHP error
- Verifikasi semua file PHP ter-load dengan benar

## Keamanan

- Semua input data di-sanitize menggunakan prepared statements
- XSS protection dengan `htmlspecialchars()`
- SQL injection prevention dengan parameter binding
- File upload validation untuk type dan size

## Dukungan

Jika mengalami masalah:
1. Cek error log PHP dan web server
2. Verifikasi konfigurasi database
3. Pastikan semua permission folder benar
4. Refresh browser dan clear cache

## Lisensi

Sistem ini dikembangkan untuk keperluan internal SIS Smart Integrator Solution.
