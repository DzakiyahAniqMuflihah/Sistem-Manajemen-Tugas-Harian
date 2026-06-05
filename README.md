## Sistem Manajemen Tugas Harian
**Tugas Besar Pemrograman Web**

## Deskripsi
Aplikasi web dinamis berbasis PHP untuk mengelola tugas harian, dilengkapi sistem login, manajemen pengguna, dan fitur CRUD lengkap.

## Fitur Utama
- Login & Logout dengan session
- Role: Admin & User
- CRUD Tugas (Create, Read, Update, Delete)
- Prioritas tugas: Tinggi / Sedang / Rendah
- Deadline dengan notifikasi visual terlambat
- Filter & Search tugas
- Dashboard statistik progress
- Validasi input & keamanan dasar

## Struktur Database
- users – data pengguna (id, nama, username, password, role)
- tugas – data tugas (id, user_id, judul, deskripsi, kategori, prioritas, status, deadline)

## Struktur Folder
sistem-manajemen-tugas-harian/
├── index.php          # Halaman login
├── logout.php         # Proses logout
├── sistem_manajemen_tugas_harian.sql       # File database
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

## Tim Pengembang
| Nama | NIM | Tugas |
|------|-----|-------|
| Dzakiyah Aniq Muflihah | 24081010254 | Full Stack (Frontend, Backend, Database) |
| Balgis Salsa Eca Hidayanti | 24081010071 | Quality Assurance / Testing |
