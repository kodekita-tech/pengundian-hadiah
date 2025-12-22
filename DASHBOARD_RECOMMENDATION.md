# Rekomendasi Dashboard Admin - Sistem Pengundian CFD

## Overview

Dokumen ini berisi rekomendasi lengkap untuk halaman dashboard admin yang akan mengikuti prinsip **logic di Service, bukan di Controller**.

---

## 1. Statistik Utama (Card Widgets)

### 1.1 Total Event

-   **Icon**: `fi fi-rr-calendar` atau `fi fi-sr-calendar`
-   **Warna**: Primary (Biru)
-   **Logic**:
    -   Super Admin/Developer: Semua event
    -   Admin Penyelenggara: Hanya event OPD-nya
-   **Service Method**: `DashboardService::getTotalEvents()`

### 1.2 Event Aktif

-   **Icon**: `fi fi-rr-play` atau `fi fi-sr-play`
-   **Warna**: Success (Hijau)
-   **Logic**: Event dengan status 'aktif' atau dalam periode pendaftaran
-   **Service Method**: `DashboardService::getActiveEvents()`
-   **Detail**: Bisa klik untuk melihat daftar event aktif

### 1.3 Total Peserta

-   **Icon**: `fi fi-rr-users` atau `fi fi-sr-users`
-   **Warna**: Info (Cyan)
-   **Logic**:
    -   Super Admin/Developer: Semua peserta
    -   Admin Penyelenggara: Peserta dari event OPD-nya
-   **Service Method**: `DashboardService::getTotalParticipants()`

### 1.4 Total Pemenang

-   **Icon**: `fi fi-rr-trophy` atau `fi fi-sr-trophy`
-   **Warna**: Warning (Kuning/Orange)
-   **Logic**: Semua pemenang yang sudah diundi
-   **Service Method**: `DashboardService::getTotalWinners()`

### 1.5 Total OPD (Hanya Super Admin/Developer)

-   **Icon**: `fi fi-rr-building` atau `fi fi-sr-building`
-   **Warna**: Secondary (Abu-abu)
-   **Logic**: Jumlah OPD terdaftar
-   **Service Method**: `DashboardService::getTotalOpds()`
-   **Visibility**: Hanya untuk role `superadmin` dan `developer`

### 1.6 Total User (Hanya Super Admin/Developer)

-   **Icon**: `fi fi-rr-user` atau `fi fi-sr-user`
-   **Warna**: Dark
-   **Logic**: Jumlah user aktif
-   **Service Method**: `DashboardService::getTotalUsers()`
-   **Visibility**: Hanya untuk role `superadmin` dan `developer`

### Layout Card Widgets:

```
┌─────────────┬─────────────┬─────────────┬─────────────┐
│ Total Event │Event Aktif  │Total Peserta│Total Pemenang│
└─────────────┴─────────────┴─────────────┴─────────────┘
┌─────────────┬─────────────┐
│ Total OPD   │ Total User  │ (jika Super Admin)
└─────────────┴─────────────┘
```

**Responsive**:

-   Desktop: 4 kolom untuk 4 card pertama
-   Tablet: 2 kolom
-   Mobile: 1 kolom

---

## 2. Grafik dan Visualisasi

### 2.1 Grafik Event per Bulan (Line Chart)

-   **Tipe**: Line Chart
-   **Data**: Jumlah event yang dibuat per bulan (6-12 bulan terakhir)
-   **Library**: Chart.js atau ApexCharts
-   **Service Method**: `DashboardService::getEventChartData($months = 6)`
-   **Warna**: Primary gradient
-   **Interaksi**: Hover untuk melihat detail bulan

**Data Structure:**

```php
[
    'labels' => ['Jan', 'Feb', 'Mar', ...],
    'data' => [5, 8, 12, ...],
    'total' => 45
]
```

### 2.2 Grafik Peserta per Event (Bar Chart)

-   **Tipe**: Horizontal Bar Chart
-   **Data**: Top 5-10 event dengan peserta terbanyak
-   **Service Method**: `DashboardService::getTopEventsByParticipants($limit = 10)`
-   **Warna**: Info gradient
-   **Interaksi**: Klik bar untuk melihat detail event

**Data Structure:**

```php
[
    ['event_name' => 'Event A', 'participants' => 150],
    ['event_name' => 'Event B', 'participants' => 120],
    ...
]
```

### 2.3 Grafik Status Event (Pie/Donut Chart)

-   **Tipe**: Donut Chart
-   **Data**: Distribusi event berdasarkan status
-   **Service Method**: `DashboardService::getEventStatusDistribution()`
-   **Warna**:
    -   Aktif: Success (Hijau)
    -   Tidak Aktif: Secondary (Abu-abu)
-   **Interaksi**: Klik segment untuk filter event

**Data Structure:**

```php
[
    ['status' => 'aktif', 'count' => 15, 'color' => '#28a745'],
    ['status' => 'tidak_aktif', 'count' => 8, 'color' => '#6c757d'],
]
```

### 2.4 Tren Pendaftaran Peserta (Line Chart)

-   **Tipe**: Line Chart dengan area fill
-   **Data**: Jumlah pendaftaran peserta per hari (7-30 hari terakhir)
-   **Service Method**: `DashboardService::getParticipantRegistrationTrend($days = 7)`
-   **Warna**: Success gradient dengan fill
-   **Filter**: Toggle antara 7 hari, 30 hari, atau custom range

**Data Structure:**

```php
[
    'labels' => ['2024-01-01', '2024-01-02', ...],
    'data' => [25, 30, 45, ...],
    'total' => 200
]
```

### Layout Grafik:

```
┌─────────────────────────────────────┐
│  Grafik Event per Bulan (Line)     │
└─────────────────────────────────────┘
┌──────────────────┬──────────────────┐
│ Status Event     │ Peserta per Event│
│ (Donut)          │ (Bar)            │
└──────────────────┴──────────────────┘
┌─────────────────────────────────────┐
│  Tren Pendaftaran (Line Area)       │
└─────────────────────────────────────┘
```

---

## 3. Tabel Event Terbaru

### 3.1 Daftar Event

-   **Jumlah**: 5-10 event terbaru
-   **Service Method**: `DashboardService::getRecentEvents($limit = 10)`
-   **Kolom**:
    1. **Nama Event** - `nm_event`
    2. **OPD** - Nama penyelenggara (dari relasi)
    3. **Status** - Badge dengan warna sesuai status
    4. **Peserta** - Jumlah peserta terdaftar
    5. **Pemenang** - Jumlah pemenang
    6. **Tanggal** - `tgl_mulai` - `tgl_selesai`
    7. **Action** - Tombol "Lihat Detail" / "Edit"

### 3.2 Fitur Tabel

-   **Sorting**: Default by `created_at DESC`
-   **Pagination**: Jika lebih dari 10, tampilkan pagination
-   **Quick Filter**: Filter by status (dropdown)
-   **Link**: Klik nama event untuk ke detail
-   **Empty State**: Pesan jika belum ada event

### 3.3 Responsive

-   Desktop: Tabel penuh
-   Tablet/Mobile: Card layout dengan informasi penting

---

## 4. Event yang Memerlukan Perhatian

### 4.1 Event Akan Berakhir

-   **Kriteria**: Event yang `tgl_selesai` dalam 3 hari ke depan
-   **Service Method**: `DashboardService::getEventsEndingSoon($days = 3)`
-   **Tampilan**: Card dengan icon warning
-   **Action**: Link ke halaman event untuk update tanggal jika perlu

### 4.2 Event Tanpa Peserta

-   **Kriteria**: Event yang sudah dibuat tapi belum ada peserta
-   **Service Method**: `DashboardService::getEventsWithoutParticipants()`
-   **Tampilan**: Card dengan icon info
-   **Action**: Link ke halaman import peserta

### 4.3 Top Event (Peserta Terbanyak)

-   **Kriteria**: 5 event dengan peserta terbanyak
-   **Service Method**: `DashboardService::getTopEventsByParticipants($limit = 5)`
-   **Tampilan**: List dengan badge jumlah peserta
-   **Action**: Link ke detail event

### 4.4 Event Belum Diundian

-   **Kriteria**: Event yang sudah selesai (`tgl_selesai` sudah lewat) tapi belum ada pemenang
-   **Service Method**: `DashboardService::getEventsNotDrawn()`
-   **Tampilan**: Card dengan icon alert
-   **Action**: Link ke halaman pengundian

### Layout Alert Cards:

```
┌─────────────────────────────────────┐
│ ⚠️ Event Akan Berakhir (3)          │
│ - Event A (2 hari lagi)            │
│ - Event B (1 hari lagi)            │
└─────────────────────────────────────┘
┌─────────────────────────────────────┐
│ ℹ️ Event Tanpa Peserta (2)          │
│ - Event C                            │
│ - Event D                            │
└─────────────────────────────────────┘
```

---

## 5. Aktivitas Terkini (Activity Feed)

### 5.1 Jenis Aktivitas

1. **Event Baru** - "Event 'X' dibuat oleh [User]"
2. **Peserta Terdaftar** - "[N] peserta baru terdaftar di Event 'X'"
3. **Pengundian** - "Pengundian dilakukan untuk Event 'X'"
4. **User Baru** - "User '[Name]' ditambahkan"
5. **Event Status Changed** - "Status Event 'X' diubah menjadi [Status]"

### 5.2 Tampilan

-   **Format**: Timeline/Feed style
-   **Jumlah**: 10 aktivitas terbaru
-   **Service Method**: `DashboardService::getRecentActivities($limit = 10)`
-   **Icon**: Sesuai jenis aktivitas
-   **Timestamp**: Relative time (2 jam yang lalu, kemarin, dll)

### 5.3 Data Source

-   **Option 1**: Query langsung dari database (events, participants, winners)
-   **Option 2**: Activity Log table (jika ada)
-   **Rekomendasi**: Mulai dengan Option 1, bisa upgrade ke Option 2 nanti

### Layout Activity Feed:

```
┌─────────────────────────────────────┐
│ Aktivitas Terkini                  │
├─────────────────────────────────────┤
│ 🎉 Event 'CFD 2024' dibuat         │
│    2 jam yang lalu                  │
├─────────────────────────────────────┤
│ 👥 25 peserta baru terdaftar       │
│    5 jam yang lalu                  │
├─────────────────────────────────────┤
│ 🏆 Pengundian dilakukan            │
│    Kemarin                          │
└─────────────────────────────────────┘
```

---

## 6. Quick Actions

### 6.1 Tombol CTA

-   **Buat Event Baru** - Link ke `/admin/event/create`
-   **Import Peserta** - Modal atau link ke halaman import
-   **Lihat Semua Event** - Link ke `/admin/event`
-   **Manajemen User** - Link ke `/admin/users` (hanya Super Admin)

### 6.2 Tampilan

-   **Style**: Button group atau card dengan icon
-   **Layout**: Horizontal atau grid
-   **Responsive**: Stack di mobile

### Layout Quick Actions:

```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ + Event Baru │ Import Data  │ Semua Event  │ Manage User  │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

---

## 7. Statistik Berdasarkan Role

### 7.1 Super Admin / Developer

-   **Akses**: Semua data sistem
-   **Widget**: Semua 6 card (Total Event, Event Aktif, Peserta, Pemenang, OPD, User)
-   **Grafik**: Semua grafik dengan data global
-   **Tabel**: Semua event
-   **Activity**: Semua aktivitas

### 7.2 Admin Penyelenggara

-   **Akses**: Hanya data OPD-nya
-   **Widget**: 4 card (Total Event, Event Aktif, Peserta, Pemenang) - filter by OPD
-   **Grafik**: Grafik dengan data OPD-nya saja
-   **Tabel**: Hanya event OPD-nya
-   **Activity**: Hanya aktivitas event OPD-nya
-   **Quick Actions**: Tidak ada "Manage User"

### 7.3 Service Implementation

```php
// DashboardService.php
public function getStatistics(): array
{
    $user = Auth::user();

    $query = Event::query();

    // Filter berdasarkan role
    if ($user->role === 'admin_penyelenggara') {
        $query->where('opd_id', $user->opd_id);
    }

    // ... logic lainnya
}
```

---

## 8. Informasi Sistem (Opsional)

### 8.1 System Info Card

-   **Versi Aplikasi**: Dari config atau constant
-   **Database Size**: Query database size (opsional)
-   **Server Time**: Waktu server saat ini
-   **Timezone**: Asia/Jakarta

### 8.2 Tampilan

-   **Style**: Card kecil di sidebar atau footer
-   **Visibility**: Hanya untuk Super Admin/Developer (opsional)

---

## 9. Implementasi Service Class

### 9.1 DashboardService Structure

```php
<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Winner;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get all statistics for dashboard
     */
    public function getStatistics(): array
    {
        $user = Auth::user();

        return [
            'total_events' => $this->getTotalEvents($user),
            'active_events' => $this->getActiveEvents($user),
            'total_participants' => $this->getTotalParticipants($user),
            'total_winners' => $this->getTotalWinners($user),
            'total_opds' => $this->getTotalOpds($user),
            'total_users' => $this->getTotalUsers($user),
        ];
    }

    /**
     * Get total events count
     */
    public function getTotalEvents($user): int
    {
        $query = Event::query();

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->count();
    }

    /**
     * Get active events count
     */
    public function getActiveEvents($user): int
    {
        $query = Event::where('status', 'aktif');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->count();
    }

    /**
     * Get total participants count
     */
    public function getTotalParticipants($user): int
    {
        if ($user->role === 'admin_penyelenggara') {
            return Participant::whereHas('event', function($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            })->count();
        }

        return Participant::count();
    }

    /**
     * Get total winners count
     */
    public function getTotalWinners($user): int
    {
        if ($user->role === 'admin_penyelenggara') {
            return Winner::whereHas('event', function($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            })->count();
        }

        return Winner::count();
    }

    /**
     * Get total OPDs (only for super admin/developer)
     */
    public function getTotalOpds($user): ?int
    {
        if (!in_array($user->role, ['superadmin', 'developer'])) {
            return null;
        }

        return Opd::count();
    }

    /**
     * Get total users (only for super admin/developer)
     */
    public function getTotalUsers($user): ?int
    {
        if (!in_array($user->role, ['superadmin', 'developer'])) {
            return null;
        }

        return User::count();
    }

    /**
     * Get recent events
     */
    public function getRecentEvents(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();

        $query = Event::with(['opd', 'participants', 'winners'])
            ->orderBy('created_at', 'desc');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->take($limit)->get();
    }

    /**
     * Get events ending soon
     */
    public function getEventsEndingSoon(int $days = 3): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();
        $endDate = Carbon::now()->addDays($days);

        $query = Event::where('tgl_selesai', '<=', $endDate)
            ->where('tgl_selesai', '>=', Carbon::now())
            ->orderBy('tgl_selesai', 'asc');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->get();
    }

    /**
     * Get events without participants
     */
    public function getEventsWithoutParticipants(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();

        $query = Event::doesntHave('participants')
            ->orderBy('created_at', 'desc');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->get();
    }

    /**
     * Get top events by participants
     */
    public function getTopEventsByParticipants(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();

        $query = Event::withCount('participants')
            ->orderBy('participants_count', 'desc')
            ->take($limit);

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->get();
    }

    /**
     * Get events not drawn yet
     */
    public function getEventsNotDrawn(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();
        $now = Carbon::now();

        $query = Event::where('tgl_selesai', '<', $now)
            ->doesntHave('winners')
            ->orderBy('tgl_selesai', 'desc');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->get();
    }

    /**
     * Get event chart data (events per month)
     */
    public function getEventChartData(int $months = 6): array
    {
        $user = Auth::user();
        $startDate = Carbon::now()->subMonths($months);

        $query = Event::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month', 'asc');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        $data = $query->get();

        $labels = [];
        $counts = [];

        // Fill missing months with 0
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $monthLabel = Carbon::now()->subMonths($i)->format('M Y');

            $labels[] = $monthLabel;
            $counts[] = $data->firstWhere('month', $month)->count ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $counts,
            'total' => array_sum($counts),
        ];
    }

    /**
     * Get event status distribution
     */
    public function getEventStatusDistribution(): array
    {
        $user = Auth::user();

        $query = Event::selectRaw('status, COUNT(*) as count')
            ->groupBy('status');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        $data = $query->get();

        $colors = [
            'aktif' => '#28a745',
            'tidak_aktif' => '#6c757d',
        ];

        return $data->map(function($item) use ($colors) {
            return [
                'status' => $item->status,
                'label' => ucfirst(str_replace('_', ' ', $item->status)),
                'count' => $item->count,
                'color' => $colors[$item->status] ?? '#6c757d',
            ];
        })->toArray();
    }

    /**
     * Get participant registration trend
     */
    public function getParticipantRegistrationTrend(int $days = 7): array
    {
        $user = Auth::user();
        $startDate = Carbon::now()->subDays($days);

        $query = Participant::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc');

        if ($user->role === 'admin_penyelenggara') {
            $query->whereHas('event', function($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            });
        }

        $data = $query->get();

        $labels = [];
        $counts = [];

        // Fill missing days with 0
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dateLabel = Carbon::now()->subDays($i)->format('d M');

            $labels[] = $dateLabel;
            $counts[] = $data->firstWhere('date', $date)->count ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $counts,
            'total' => array_sum($counts),
        ];
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 10): array
    {
        $user = Auth::user();
        $activities = [];

        // Recent events
        $eventsQuery = Event::with('opd')
            ->orderBy('created_at', 'desc')
            ->take(5);

        if ($user->role === 'admin_penyelenggara') {
            $eventsQuery->where('opd_id', $user->opd_id);
        }

        foreach ($eventsQuery->get() as $event) {
            $activities[] = [
                'type' => 'event_created',
                'icon' => 'fi fi-rr-calendar',
                'message' => "Event '{$event->nm_event}' dibuat",
                'time' => $event->created_at,
                'link' => route('admin.events.show', $event->id),
            ];
        }

        // Recent participants (grouped by event)
        $participantsQuery = Participant::with('event')
            ->selectRaw('event_id, COUNT(*) as count, MAX(created_at) as latest_created')
            ->groupBy('event_id')
            ->orderBy('latest_created', 'desc')
            ->take(5);

        if ($user->role === 'admin_penyelenggara') {
            $participantsQuery->whereHas('event', function($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            });
        }

        foreach ($participantsQuery->get() as $participant) {
            $activities[] = [
                'type' => 'participants_registered',
                'icon' => 'fi fi-rr-users',
                'message' => "{$participant->count} peserta baru terdaftar di Event '{$participant->event->nm_event}'",
                'time' => $participant->latest_created,
                'link' => route('admin.events.participants.index', $participant->event_id),
            ];
        }

        // Recent winners
        $winnersQuery = Winner::with(['event', 'participant'])
            ->orderBy('drawn_at', 'desc')
            ->take(5);

        if ($user->role === 'admin_penyelenggara') {
            $winnersQuery->whereHas('event', function($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            });
        }

        foreach ($winnersQuery->get() as $winner) {
            $activities[] = [
                'type' => 'draw_completed',
                'icon' => 'fi fi-rr-trophy',
                'message' => "Pengundian dilakukan untuk Event '{$winner->event->nm_event}'",
                'time' => $winner->drawn_at,
                'link' => route('admin.events.show', $winner->event_id),
            ];
        }

        // Sort by time and limit
        usort($activities, function($a, $b) {
            return $b['time'] <=> $a['time'];
        });

        return array_slice($activities, 0, $limit);
    }
}
```

---

## 10. Controller Implementation

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $statistics = $this->dashboardService->getStatistics();
        $recentEvents = $this->dashboardService->getRecentEvents(10);
        $eventsEndingSoon = $this->dashboardService->getEventsEndingSoon(3);
        $eventsWithoutParticipants = $this->dashboardService->getEventsWithoutParticipants();
        $topEvents = $this->dashboardService->getTopEventsByParticipants(5);
        $eventsNotDrawn = $this->dashboardService->getEventsNotDrawn();
        $eventChartData = $this->dashboardService->getEventChartData(6);
        $statusDistribution = $this->dashboardService->getEventStatusDistribution();
        $participantTrend = $this->dashboardService->getParticipantRegistrationTrend(7);
        $activities = $this->dashboardService->getRecentActivities(10);

        return view('admin.dashboard', compact(
            'statistics',
            'recentEvents',
            'eventsEndingSoon',
            'eventsWithoutParticipants',
            'topEvents',
            'eventsNotDrawn',
            'eventChartData',
            'statusDistribution',
            'participantTrend',
            'activities'
        ));
    }
}
```

---

## 11. Prioritas Implementasi

### Phase 1 (MVP - Minimum Viable Product):

1. ✅ Statistik Card Widgets (4 utama)
2. ✅ Tabel Event Terbaru
3. ✅ Quick Actions

### Phase 2 (Enhanced):

4. ✅ Grafik Event per Bulan
5. ✅ Grafik Status Event
6. ✅ Event yang Memerlukan Perhatian

### Phase 3 (Advanced):

7. ✅ Grafik Peserta per Event
8. ✅ Tren Pendaftaran Peserta
9. ✅ Aktivitas Terkini

### Phase 4 (Optional):

10. ✅ System Info
11. ✅ Advanced Filters
12. ✅ Export Dashboard Data

---

## 12. UI/UX Recommendations

### 12.1 Color Scheme

-   **Primary**: Biru (#007bff) - untuk event
-   **Success**: Hijau (#28a745) - untuk aktif/positif
-   **Warning**: Kuning/Orange (#ffc107) - untuk perhatian
-   **Info**: Cyan (#17a2b8) - untuk informasi
-   **Danger**: Merah (#dc3545) - untuk error/urgent

### 12.2 Icons

-   Gunakan Flaticon (fi fi-rr-\*) yang sudah digunakan di project
-   Konsisten dengan icon yang sama untuk fungsi yang sama

### 12.3 Loading States

-   Skeleton loader untuk card widgets
-   Loading spinner untuk grafik
-   Progressive loading untuk tabel

### 12.4 Empty States

-   Pesan yang jelas jika tidak ada data
-   Call-to-action untuk membuat data pertama

### 12.5 Responsive Design

-   Mobile-first approach
-   Breakpoints: Mobile (< 768px), Tablet (768-1024px), Desktop (> 1024px)

---

## 13. Performance Considerations

### 13.1 Query Optimization

-   Gunakan `with()` untuk eager loading
-   Gunakan `select()` untuk limit columns
-   Index pada kolom yang sering di-query (`opd_id`, `status`, `created_at`)

### 13.2 Caching (Future Enhancement)

-   Cache statistics untuk 5-10 menit
-   Cache chart data untuk 15 menit
-   Invalidate cache saat ada perubahan data

### 13.3 Lazy Loading

-   Load grafik secara async
-   Pagination untuk tabel besar
-   Infinite scroll untuk activity feed (opsional)

---

## 14. Testing Recommendations

### 14.1 Unit Tests

-   Test setiap method di `DashboardService`
-   Test role-based filtering
-   Test edge cases (empty data, null values)

### 14.2 Feature Tests

-   Test dashboard page load
-   Test dengan berbagai role
-   Test responsive layout

---

## 15. Next Steps

1. **Buat Service Class**: `app/Services/DashboardService.php`
2. **Update Controller**: Refactor `DashboardController`
3. **Buat View**: Update `resources/views/admin/dashboard.blade.php`
4. **Add Charts Library**: Install Chart.js atau ApexCharts
5. **Test**: Manual testing dengan berbagai role
6. **Optimize**: Query optimization dan caching jika perlu

---

**Catatan**: Semua logic harus berada di `DashboardService`, controller hanya memanggil service dan mengembalikan view dengan data.
