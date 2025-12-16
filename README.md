# Sistem Pengundian CFD

Sistem manajemen pengundian untuk event dengan fitur pendaftaran, pengundian, dan manajemen OPD (Organisasi Perangkat Daerah).

## Tentang Project

Sistem Pengundian CFD adalah aplikasi web berbasis Laravel yang dirancang untuk mengelola event pengundian dengan fitur pendaftaran peserta, manajemen OPD, dan sistem pengundian yang transparan. Aplikasi ini dilengkapi dengan QR Code untuk akses event dan fitur import/export data.

## Fitur Utama

### 1. Manajemen User

-   CRUD User dengan role-based access control
-   Role: Super Admin, Developer, Admin OPD
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
-   QR Code untuk akses event
-   Tanggal mulai dan selesai event
-   Deskripsi event
-   Relasi dengan OPD

### 4. Dashboard Admin

-   Overview sistem
-   Statistik dan monitoring

### 5. Profile Management

-   Edit profile user
-   Update password

### 6. Fitur Tambahan

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
│   │   │       ├── ProfileController.php
│   │   │       └── UserController.php
│   │   └── Requests/
│   │       ├── StoreEventRequest.php
│   │       ├── StoreUserRequest.php
│   │       └── UpdateUserRequest.php
│   └── Models/
│       ├── Event.php
│       ├── Opd.php
│       └── User.php
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

### Admin OPD

-   Manajemen event untuk OPD sendiri
-   Akses terbatas sesuai OPD yang ditugaskan

## Database Schema

### Users

-   id, name, email, password, role, opd_id, created_at, updated_at

### OPD

-   id, nama_instansi, singkatan, nomor_hp, created_at, updated_at

### Event

-   id, nm_event, opd_id, status, tgl_mulai, tgl_selesai, deskripsi, qr_token, created_at, updated_at

## API Endpoints

### Admin Routes

-   `/admin/dashboard` - Dashboard
-   `/admin/users` - User Management
-   `/admin/opd` - OPD Management
-   `/admin/event` - Event Management
-   `/admin/profile` - Profile Management

### Guest Routes

-   `/event/{token}` - Public event access via QR token

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
