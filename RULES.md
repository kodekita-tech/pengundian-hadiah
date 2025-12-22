# Rules & Guidelines - Sistem Pengundian CFD

## 1. Pemisahan Logic dari Controller

### Prinsip Dasar

**Controller hanya bertugas sebagai HTTP layer yang menerima request dan mengembalikan response. Semua business logic HARUS dipisahkan dari controller.**

### Struktur yang Harus Diikuti

```
app/
├── Http/
│   └── Controllers/        # Hanya HTTP handling
│       ├── Admin/
│       └── Guest/
├── Services/               # Business logic layer (BUAT FOLDER INI)
│   ├── EventService.php
│   ├── ParticipantService.php
│   ├── DrawService.php
│   ├── DashboardService.php
│   └── UserService.php
├── Repositories/          # Data access layer (OPSIONAL, jika perlu)
│   ├── EventRepository.php
│   └── ParticipantRepository.php
└── Models/                # Eloquent models dengan relasi dan accessor
```

### Aturan Controller

#### ✅ YANG BOLEH di Controller:

1. **Validasi Request** - menggunakan Form Request classes
2. **Memanggil Service** - untuk business logic
3. **Mengembalikan Response** - view, JSON, redirect
4. **Authorization Check** - middleware atau policy
5. **Session Management** - untuk flash messages

#### ❌ YANG TIDAK BOLEH di Controller:

1. **Query Database Kompleks** - pindahkan ke Service/Repository
2. **Business Logic** - validasi bisnis, perhitungan, transformasi data
3. **Transaction Management** - pindahkan ke Service
4. **File Processing** - import/export logic pindahkan ke Service
5. **Perhitungan Statistik** - pindahkan ke Service
6. **Conditional Logic Kompleks** - if/else panjang pindahkan ke Service

### Contoh Implementasi

#### ❌ SALAH - Logic di Controller:

```php
// app/Http/Controllers/Admin/DashboardController.php
public function index()
{
    $totalEvents = Event::count();
    $activeEvents = Event::where('status', 'aktif')->count();
    $totalParticipants = Participant::count();

    // Logic kompleks di controller
    $events = Event::with('opd', 'participants')
        ->where('tgl_selesai', '>=', now())
        ->orderBy('tgl_mulai', 'desc')
        ->take(5)
        ->get()
        ->map(function($event) {
            return [
                'name' => $event->nm_event,
                'participants' => $event->participants->count(),
                'winners' => $event->winners->count(),
            ];
        });

    return view('admin.dashboard', compact('totalEvents', 'activeEvents', 'totalParticipants', 'events'));
}
```

#### ✅ BENAR - Logic di Service:

```php
// app/Services/DashboardService.php
namespace App\Services;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Winner;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getStatistics(): array
    {
        $user = Auth::user();

        return [
            'total_events' => $this->getTotalEvents($user),
            'active_events' => $this->getActiveEvents($user),
            'total_participants' => $this->getTotalParticipants($user),
            'total_winners' => $this->getTotalWinners($user),
        ];
    }

    public function getRecentEvents(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();
        $query = Event::with(['opd', 'participants', 'winners'])
            ->where('tgl_selesai', '>=', now())
            ->orderBy('tgl_mulai', 'desc');

        // Filter berdasarkan role
        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->take($limit)->get();
    }

    private function getTotalEvents($user): int
    {
        $query = Event::query();

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->count();
    }

    // ... methods lainnya
}
```

```php
// app/Http/Controllers/Admin/DashboardController.php
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
        $recentEvents = $this->dashboardService->getRecentEvents(5);

        return view('admin.dashboard', compact('statistics', 'recentEvents'));
    }
}
```

### Service Class Pattern

#### Struktur Service Class:

```php
<?php

namespace App\Services;

use App\Models\YourModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class YourService
{
    /**
     * Constructor - Dependency Injection
     */
    public function __construct()
    {
        // Inject dependencies jika perlu
    }

    /**
     * Public method - Main business logic
     */
    public function doSomething(array $data): array
    {
        try {
            DB::beginTransaction();

            $result = $this->processData($data);

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in YourService: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Private method - Helper logic
     */
    private function processData(array $data): array
    {
        // Implementation
    }
}
```

### Kapan Menggunakan Service vs Repository

#### Service Layer:

-   **Business Logic** - validasi bisnis, perhitungan
-   **Transaction Management** - DB transactions
-   **Multiple Model Operations** - operasi yang melibatkan beberapa model
-   **Complex Queries** - query dengan logic kompleks
-   **Data Transformation** - transformasi data untuk response
-   **File Processing** - import/export, file handling

#### Repository Layer (OPSIONAL):

-   **Simple CRUD** - jika hanya butuh abstraction untuk query
-   **Reusable Queries** - query yang digunakan di banyak tempat
-   **Testing** - memudahkan mocking untuk testing

**Catatan:** Untuk project ini, Service layer sudah cukup. Repository layer hanya diperlukan jika ada kebutuhan khusus.

### Dependency Injection

#### ✅ Gunakan Constructor Injection:

```php
class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
}
```

#### ✅ Atau Method Injection:

```php
public function index(DashboardService $dashboardService)
{
    $data = $dashboardService->getStatistics();
    return view('admin.dashboard', compact('data'));
}
```

### Error Handling

#### Di Service:

```php
public function createEvent(array $data): Event
{
    try {
        DB::beginTransaction();

        // Business logic
        $event = Event::create($data);

        DB::commit();

        return $event;
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating event: ' . $e->getMessage(), [
            'data' => $data,
            'user_id' => Auth::id(),
        ]);
        throw $e; // Re-throw untuk ditangani di controller
    }
}
```

#### Di Controller:

```php
public function store(StoreEventRequest $request, EventService $eventService)
{
    try {
        $event = $eventService->createEvent($request->validated());

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil dibuat.');
    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Gagal membuat event: ' . $e->getMessage());
    }
}
```

## 2. Naming Conventions

### Service Classes:

-   Nama: `{Model}Service.php` atau `{Feature}Service.php`
-   Contoh: `EventService.php`, `DashboardService.php`, `DrawService.php`

### Service Methods:

-   Gunakan verb yang jelas: `get`, `create`, `update`, `delete`, `process`
-   Contoh: `getStatistics()`, `createEvent()`, `processDraw()`

## 3. Testing Considerations

Dengan memisahkan logic ke Service, testing menjadi lebih mudah:

```php
// tests/Unit/Services/DashboardServiceTest.php
public function test_get_statistics_returns_correct_data()
{
    $service = new DashboardService();
    $stats = $service->getStatistics();

    $this->assertArrayHasKey('total_events', $stats);
    $this->assertIsInt($stats['total_events']);
}
```

## 4. Checklist Refactoring

Saat memindahkan logic dari controller ke service:

-   [ ] Identifikasi semua query database di controller
-   [ ] Identifikasi semua business logic di controller
-   [ ] Buat Service class yang sesuai
-   [ ] Pindahkan logic ke Service
-   [ ] Update controller untuk menggunakan Service
-   [ ] Test functionality masih bekerja
-   [ ] Update dokumentasi jika perlu

## 5. Contoh Service yang Perlu Dibuat

Berdasarkan controller yang ada:

1. **DashboardService** - Statistik dan data dashboard
2. **EventService** - CRUD dan logic event
3. **ParticipantService** - Import/export dan management peserta
4. **DrawService** - Logic pengundian
5. **UserService** - Management user dan role
6. **QrService** - Generate dan validasi QR code

---

**Catatan Penting:**

-   Rules ini harus diikuti untuk semua development baru
-   Controller yang sudah ada bisa direfactor secara bertahap
-   Prioritas: DashboardService untuk dashboard admin yang akan dibuat
