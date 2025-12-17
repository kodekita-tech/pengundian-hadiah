# Sistem Pengundian CFD

Sistem manajemen pengundian untuk event dengan fitur pendaftaran, pengundian, dan manajemen OPD (Organisasi Perangkat Daerah).

## Tentang Project

Sistem Pengundian CFD adalah aplikasi web berbasis Laravel yang dirancang untuk mengelola event pengundian dengan fitur pendaftaran peserta, manajemen OPD, dan sistem pengundian yang transparan. Aplikasi ini dilengkapi dengan QR Code untuk akses event dan fitur import/export data.

## Fitur Utama

### 1. Manajemen User

-   CRUD User dengan role-based access control
-   Role: Super Admin, Developer, Admin Penyelenggara
-   Setiap user dapat dikaitkan dengan OPD tertentu
-   Import/Export user via Excel
-   Validasi dan keamanan data

### 2. Manajemen OPD (Organisasi Perangkat Daerah)

-   CRUD OPD
-   Data instansi, singkatan, dan nomor HP
-   Relasi dengan user dan event

### 3. Manajemen Event

-   CRUD Event dengan status:
    -   Draft
    -   Pendaftaran Dibuka
    -   Pendaftaran Ditutup
    -   Pengundian
    -   Selesai
-   Generate QR Code & Shortlink untuk akses event
-   Pengaturan Passkey untuk keamanan area pengundian
-   Monitoring statistik peserta real-time

### 4. Manajemen Peserta

-   Import data peserta massal via Excel
-   Export data peserta per event
-   Manajemen data peserta (Edit/Delete)
-   Fitur "Clear Data" untuk reset peserta event
-   Validasi data peserta (NIK/No HP unik per event)

### 5. Sistem Pengundian (Drawing System)

-   Halaman pengundian khusus (Guest View)
-   Akses via Shortlink unik (contoh: domain/d/xyz123)
-   Proteksi halaman undian dengan Passkey
-   Animasi pengundian interaktif
-   Pemilihan pemenang secara acak dari peserta terdaftar
-   Penyimpanan data pemenang otomatis

### 6. Registrasi Mandiri (Self Registration)

-   Akses via Scan QR Code Event
-   Form pendaftaran publik
-   Proteksi Captcha untuk mencegah spam
-   Validasi input real-time

### 7. Dashboard Admin

-   Overview sistem
-   Statistik dan monitoring

### 8. Profile Management

-   Edit profile user
-   Update password

### 9. Fitur Tambahan

-   Import/Export Excel (Maatwebsite Excel)
-   DataTables untuk tabel interaktif
-   QR Code generation (Endroid QR Code)
-   Select2 untuk dropdown dengan AJAX
-   Authentication dengan Laravel UI

## Teknologi yang Digunakan

-   **Framework**: Laravel 12
-   **PHP**: 8.2+
-   **Database**: MySQL/SQLite
-   **Frontend**:
    -   Bootstrap 5
    -   jQuery
    -   DataTables
    -   Select2
    -   SweetAlert2
-   **Libraries**:
    -   Maatwebsite Excel (Import/Export)
    -   Yajra DataTables (Server-side processing)
    -   Endroid QR Code (QR Code generation)

## Requirements

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   Database (MySQL/MariaDB/PostgreSQL/SQLite)

## Instalasi

1. Clone repository

```bash
git clone <repository-url>
cd pengundian-cfd
```

2. Install dependencies

```bash
composer install
npm install
```

3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

4. Konfigurasi database di `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengundian_cfd
DB_USERNAME=root
DB_PASSWORD=
```

5. Jalankan migration dan seeder

```bash
php artisan migrate
php artisan db:seed
```

6. Build assets

```bash
npm run build
```

7. Jalankan server

```bash
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

## Struktur Project

```
pengundian-cfd/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── EventController.php
│   │   │       ├── OpdController.php
│   │   │       ├── ParticipantController.php
│   │   │       ├── ProfileController.php
│   │   │       └── UserController.php
│   │   └── Requests/
│   │       ├── StoreEventRequest.php
│   │       ├── StoreUserRequest.php
│   │       └── UpdateUserRequest.php
│   └── Models/
│       ├── Event.php
│       ├── Opd.php
│       ├── Participant.php
│       ├── User.php
│       └── Winner.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── event/
│       │   ├── opd/
│       │   ├── users/
│       │   └── profile/
│       └── guest/
│           └── event/
└── routes/
    ├── admin/
    └── guest/
```

## Role dan Permission

### Super Admin

-   Akses penuh ke semua fitur
-   Manajemen semua user dan OPD
-   Manajemen semua event

### Developer

-   Akses penuh ke semua fitur
-   Mirip dengan Super Admin

### Admin Penyelenggara

-   Manajemen event untuk OPD sendiri
-   Akses terbatas sesuai OPD yang ditugaskan

## Database Schema

### Users

-   id, name, email, password, role, opd_id, created_at, updated_at

### OPD

-   id, nama_penyelenggara, singkatan, nomor_hp, created_at, updated_at

### Event

-   id, nm_event, opd_id, status, tgl_mulai, tgl_selesai, deskripsi, qr_token, created_at, updated_at
-   status: "draft", "pendaftaran_dibuka", "pendaftaran_ditutup", "pengundian", "selesai"

### Participants

-   id, event_id, name, phone, coupon_number, is_winner (boolean), created_at, updated_at

### Winners

-   id, event_id, participant_id, prize_name, drawn_at, created_at, updated_at

## API Endpoints

### Admin Routes

-   `/admin/dashboard` - Dashboard
-   `/admin/users` - User Management
-   `/admin/opd` - OPD Management
-   `/admin/event` - Event Management
-   `/admin/event/{id}/participants` - Participant Management
-   `/admin/profile` - Profile Management

### Guest Routes

-   `/qr/{token}` - Public registration via QR token
-   `/d/{shortlink}` - Drawing Access (Shortlink)

## Development

### Menjalankan Development Server

```bash
composer run dev
```

### Testing

```bash
php artisan test
```

### Code Style

```bash
./vendor/bin/pint
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
