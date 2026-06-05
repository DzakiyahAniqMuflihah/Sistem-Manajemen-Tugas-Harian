# 📋 Sistem Manajemen Tugas Harian
**Tugas Besar Pemrograman Web – UPN Veteran Jawa Timur**

## Deskripsi
Aplikasi web dinamis berbasis PHP untuk mengelola tugas harian, dilengkapi sistem login, manajemen pengguna, dan fitur CRUD lengkap.

## Fitur Utama
- 🔐 Login & Logout dengan session
- 👤 Role: Admin & User
- ✅ CRUD Tugas (Create, Read, Update, Delete)
- 🎯 Prioritas tugas: Tinggi / Sedang / Rendah
- 📅 Deadline dengan notifikasi visual terlambat
- 🔍 Filter & Search tugas
- 📊 Dashboard statistik progress
- 🔒 Validasi input & keamanan dasar

## Struktur Database
- `users` – data pengguna (id, nama, username, password, role)
- `tugas` – data tugas (id, user_id, judul, deskripsi, kategori, prioritas, status, deadline)

## Cara Instalasi
1. Clone/download project ini ke folder `htdocs/` XAMPP
2. Buka phpMyAdmin, buat database `db_taskmanager`
3. Import file `database.sql`
4. Jalankan XAMPP (Apache + MySQL)
5. Akses: `localhost/taskmanager/`

## Akun Default
| Username | Password | Role  |
|----------|----------|-------|
| admin    | admin123 | Admin |
| budi     | budi123  | User  |
| siti     | siti123  | User  |

## Struktur Folder
```
taskmanager/
├── index.php          # Halaman login
├── logout.php         # Proses logout
├── database.sql       # File database
├── css/
│   └── style.css      # Stylesheet
├── includes/
│   ├── koneksi.php    # Koneksi database
│   └── auth.php       # Helper autentikasi
├── admin/
│   ├── dashboard.php  # Dashboard admin
│   ├── tugas.php      # Kelola semua tugas
│   ├── edit_tugas.php # Edit tugas
│   └── users.php      # Kelola user
└── user/
    ├── dashboard.php  # Dashboard user
    ├── tugas.php      # Daftar tugas saya
    ├── tambah_tugas.php # Form tambah tugas
    ├── edit_tugas.php # Edit tugas
    └── profil.php     # Profil & ganti password
```

## Teknologi
- PHP 7.4+
- MySQL / MariaDB
- HTML5 + CSS3 (custom, tanpa framework)
- XAMPP

## Tim Pengembang
| Nama | NIM | Tugas |
|------|-----|-------|
| [Nama Anggota 1] | [NIM] | Backend (Login, Auth, DB) |
| [Nama Anggota 2] | [NIM] | Frontend (UI, CRUD, Filter) |
