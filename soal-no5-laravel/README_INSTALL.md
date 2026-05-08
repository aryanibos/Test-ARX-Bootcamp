# Panduan Instalasi Soal Nomor 5

Project ini adalah aplikasi **Register, Login, dan CMS User sederhana** menggunakan Laravel.

Fitur utama:

- Login user menggunakan AJAX.
- CRUD user menggunakan AJAX.
- Upload image profile.
- List user menggunakan jQuery DataTables server-side.
- Pagination 10 data per halaman.
- Database menggunakan SQLite.
- Penyimpanan gambar profile di folder `public/profile-images`.

## Requirement

Pastikan sudah terinstall:

- PHP minimal 8.2
- Composer
- Browser

Project ini menggunakan SQLite bawaan Laravel, jadi tidak wajib setup MySQL.

## Cara Install dari Awal

Masuk ke folder project:

```bash
cd soal-no5-laravel
```

Install dependency Laravel:

```bash
composer install
```

Copy file environment:

```bash
copy .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Buat file database SQLite jika belum ada:

```bash
type nul > database\database.sqlite
```

Jalankan migration dan seeder:

```bash
php artisan migrate:fresh --seed
```

Buat folder penyimpanan image profile jika belum ada:

```bash
mkdir public\profile-images
```

Jalankan server Laravel:

```bash
php artisan serve
```

Buka aplikasi di browser:

```text
http://127.0.0.1:8000/login
```

## Akun Login Awal

Gunakan akun berikut untuk login pertama kali:

```text
Email: arya@gmail.com
Password: bismillah123
```

Setelah login, user dapat menambah, mengubah, menghapus, dan melihat list user di halaman CMS User.

## Struktur Penyimpanan Gambar

Image profile disimpan di:

```text
public/profile-images
```

Contoh URL gambar:

```text
http://127.0.0.1:8000/profile-images/nama-file.jpg
```

## Perintah Penting

Menjalankan migration ulang dan mengisi akun awal:

```bash
php artisan migrate:fresh --seed
```

Menjalankan test:

```bash
php artisan test
```

Menjalankan format check:

```bash
vendor\bin\pint --test
```

Menjalankan server:

```bash
php artisan serve
```

## Catatan

- Semua proses create, update, delete, dan login menggunakan AJAX request.
- Email user harus unique.
- Password wajib diisi saat membuat user baru.
- Saat edit user, password boleh dikosongkan jika tidak ingin mengubah password.
- Ukuran image yang tampil di DataTables adalah 200px x 200px.
- DataTables menggunakan server-side processing.
