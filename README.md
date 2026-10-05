# File Management System (FMS)

File Management System (FMS) adalah aplikasi berbasis web untuk mengelola dokumen secara terstruktur berdasarkan folder dan department. Aplikasi memiliki dua jenis pengguna, yaitu **Administrator** dan **Viewer**, dengan hak akses yang berbeda.

## Features

### Administrator
- Login dan autentikasi pengguna
- Dashboard dengan statistik folder, file, dan department
- Manajemen department
- Manajemen folder dan subfolder secara hierarkis
- Membuat folder dengan unlimited nesting level
- Upload dokumen
- Edit informasi dokumen
- Hapus dokumen
- Download dokumen
- Melihat daftar file terbaru
- Pencarian dan filter dokumen

### Viewer
- Login sebagai Viewer
- Melihat struktur folder dan subfolder
- Melihat daftar dokumen
- Melihat detail dokumen
- Pencarian dokumen
- Filter berdasarkan department
- Download dokumen
- Tidak memiliki akses untuk melakukan perubahan data

## Tech Stack

- **Backend:** Laravel 11
- **Frontend:** Vue.js
- **CSS Framework:** Tailwind CSS
- **Database:** PostgreSQL
- **Authentication:** Laravel Breeze
- **Storage:** Laravel Filesystem
- **Testing:** PHPUnit

## Requirements

Pastikan environment berikut sudah tersedia:

- PHP >= 8.2
- Composer
- Node.js >= 20
- npm
- PostgreSQL
- Git

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/ginanjar1018/fms-lion.git
cd fms-lion
```

### 2. Install Backend Dependencies

```bash
composer install
```

### 3. Install Frontend Dependencies

```bash
npm install
```

### 4. Configure Environment

Copy file environment example:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Kemudian sesuaikan konfigurasi database PostgreSQL pada file `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fms_lion
DB_USERNAME=postgres
DB_PASSWORD=
```

> Gunakan database PostgreSQL yang tersedia pada environment masing-masing. Jangan menggunakan password database dari development environment.

### 5. Create Database

Buat database PostgreSQL dengan nama:

```text
fms_lion
```

### 6. Run Migration and Seeder

Jalankan migration:

```bash
php artisan migrate
```

Jalankan database seeder:

```bash
php artisan db:seed
```

Atau dapat dijalankan sekaligus:

```bash
php artisan migrate --seed
```

Seeder akan membuat akun Administrator, Viewer, serta beberapa data department awal.

### 7. Configure File Storage

Buat symbolic link untuk public storage:

```bash
php artisan storage:link
```

### 8. Build Frontend Assets

```bash
npm run build
```

### 9. Run Application

Jalankan Laravel development server:

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```text
http://127.0.0.1:8000
```

## Login Accounts

### Administrator

```text
Email    : admin@example.com
Password : password
Role     : Administrator
```

Administrator memiliki akses untuk mengelola folder, department, dan dokumen.

### Viewer

```text
Email    : viewer@example.com
Password : password
Role     : Viewer
```

Viewer hanya memiliki akses untuk melihat dan mengunduh dokumen tanpa dapat melakukan perubahan data.

## Cara Penggunaan

### Administrator

1. Login menggunakan akun Administrator.
2. Kelola department melalui menu **Departments**.
3. Buat folder dan subfolder melalui menu **Folders**.
4. Upload dokumen melalui menu **Files**.
5. Tentukan title, department, dan folder dokumen.
6. Edit informasi dokumen jika diperlukan.
7. Download atau hapus dokumen sesuai kebutuhan.
8. Gunakan Dashboard untuk melihat statistik dan file terbaru.

### Viewer

1. Login menggunakan akun Viewer.
2. Buka halaman **My Documents**.
3. Pilih folder untuk melihat dokumen di dalamnya.
4. Gunakan pencarian untuk menemukan dokumen.
5. Gunakan filter department jika diperlukan.
6. Pilih dokumen untuk melihat detail.
7. Download dokumen menggunakan tombol **Download**.

Viewer tidak memiliki akses terhadap fungsi create, update, atau delete.

## Database Structure

Aplikasi menggunakan beberapa entitas utama:

- **users** — menyimpan data pengguna dan role
- **departments** — menyimpan data department
- **folders** — menyimpan struktur folder dan hubungan parent-child
- **files** — menyimpan metadata dokumen dan relasinya dengan folder, department, dan uploader

Folder mendukung struktur hierarkis dengan hubungan parent-child sehingga dapat digunakan untuk membuat folder dan subfolder dengan tingkat kedalaman yang tidak dibatasi.

## Testing

Feature test tersedia untuk menguji fungsi utama aplikasi, termasuk:

- Authentication
- Folder management
- File management
- Department management
- Profile management

Jalankan seluruh test dengan:

```bash
php artisan test
```

## Security

File environment yang berisi konfigurasi lokal dan credential database tidak disimpan di repository.

Gunakan:

```text
.env.example
```

sebagai template konfigurasi environment.

File berikut tidak di-commit ke repository:

```text
.env
.env.testing
```

## Git Repository

Repository:

https://github.com/ginanjar1018/fms-lion

## Notes

- Setelah melakukan perubahan pada frontend, jalankan `npm run build`.
- Pastikan PostgreSQL aktif sebelum menjalankan aplikasi.
- Pastikan `php artisan storage:link` sudah dijalankan agar file dapat diakses melalui public storage.
- Dokumen yang di-upload melalui aplikasi disimpan menggunakan Laravel Filesystem.