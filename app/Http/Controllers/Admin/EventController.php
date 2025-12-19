<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::with(['opd'])
            ->orderBy('created_at', 'desc');

        $user = $request->user();
        
        // Filter by opd_id only for admin_penyelenggara role
        // Superadmin and Developer can see all events
        if ($user && $user->role === 'admin_penyelenggara' && $user->opd_id) {
            $query->where('opd_id', $user->opd_id);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $events = $query->get();
        
        // Auto-update status untuk semua event sebelum ditampilkan
        foreach ($events as $event) {
            $event->autoUpdateStatus();
        }

        return view('admin.event.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $canSelectOpd = in_array($user->role, ['superadmin', 'developer']);
        
        return view('admin.event.create', compact('canSelectOpd'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        
        // Jika user adalah admin_penyelenggara, set opd_id dari user (override dari form)
        if ($user->role === 'admin_penyelenggara') {
            if (empty($user->opd_id)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['opd_id' => 'User tidak memiliki Penyelenggara yang terdaftar. Silakan hubungi administrator.']);
            }
            // Force set opd_id dari user, abaikan dari form
            $data['opd_id'] = (int) $user->opd_id;
        }
        
        // Pastikan opd_id tidak null atau kosong untuk semua role
        if (!isset($data['opd_id']) || empty($data['opd_id']) || is_null($data['opd_id'])) {
            return redirect()
                ->back()
                ->withInput()
                    ->withErrors(['opd_id' => 'Penyelenggara wajib dipilih.']);
        }
        
        // Pastikan opd_id adalah integer
        $data['opd_id'] = (int) $data['opd_id'];
        
        // Hash passkey jika ada
        if (!empty($data['passkey'])) {
            $data['passkey'] = password_hash($data['passkey'], PASSWORD_BCRYPT);
        }
        
        $event = Event::create($data);

        // Generate QR token
        $event->generateQrToken();
        
        // Generate shortlink
        $event->generateShortlink();

        return redirect()
            ->route('admin.event.index')
            ->with('success', 'Event berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // Auto-update status berdasarkan tanggal sebelum ditampilkan
        $event->autoUpdateStatus();
        $event->refresh();
        
        $event->load(['opd', 'participants', 'prizes', 'winners']);
        
        // Get statistics
        $stats = [
            'participants_count' => $event->participants()->count(),
            'prizes_count' => $event->prizes()->count(),
            'winners_count' => $event->winners()->count(),
            'available_participants' => $event->participants()->where('is_winner', false)->count(),
        ];

        return view('admin.event.show', compact('event', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // Auto-update status berdasarkan tanggal sebelum ditampilkan
        $event->autoUpdateStatus();
        $event->refresh();
        
        $user = Auth::user();
        $canSelectOpd = in_array($user->role, ['superadmin', 'developer']);
        
        return view('admin.event.edit', compact('event', 'canSelectOpd'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEventRequest $request, Event $event)
    {
        $user = Auth::user();
        $data = $request->validated();
        
        // Jika user adalah admin_penyelenggara, set opd_id dari user (override dari form)
        if ($user->role === 'admin_penyelenggara') {
            if (empty($user->opd_id)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['opd_id' => 'User tidak memiliki Penyelenggara yang terdaftar. Silakan hubungi administrator.']);
            }
            // Force set opd_id dari user, abaikan dari form
            $data['opd_id'] = (int) $user->opd_id;
        }
        
        // Pastikan opd_id tidak null atau kosong untuk semua role
        if (!isset($data['opd_id']) || empty($data['opd_id']) || is_null($data['opd_id'])) {
            return redirect()
                ->back()
                ->withInput()
                    ->withErrors(['opd_id' => 'Penyelenggara wajib dipilih.']);
        }
        
        // Pastikan opd_id adalah integer
        $data['opd_id'] = (int) $data['opd_id'];
        
        // Hash passkey jika ada dan diisi
        if (!empty($data['passkey'])) {
            $data['passkey'] = password_hash($data['passkey'], PASSWORD_BCRYPT);
        } else {
            // Jika passkey tidak diisi, hapus dari data agar tidak mengoverwrite yang lama
            unset($data['passkey']);
        }
        
        $event->update($data);

        return redirect()
            ->route('admin.event.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()
            ->route('admin.event.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    /**
     * Update event status.
     */
    public function updateStatus(Request $request, Event $event)
    {
        $request->validate([
            'status' => ['required', 'in:aktif,tidak_aktif']
        ]);

        $event->update(['status' => $request->status]);

        // Regenerate QR token if status changed to active and QR token doesn't exist
        if ($request->status === Event::STATUS_ACTIVE && !$event->qr_token) {
            $event->generateQrToken();
        }

        return redirect()
            ->back()
            ->with('success', 'Status event berhasil diperbarui.');
    }

    /**
     * Regenerate QR token for event.
     */
    public function regenerateQrToken(Event $event)
    {
        $event->generateQrToken();

        return redirect()
            ->back()
            ->with('success', 'QR Token berhasil di-regenerate.');
    }

    /**
     * Get OPD data for Select2 AJAX.
     */
    public function getOpdData(Request $request)
    {
        $user = Auth::user();
        
        // Hanya superadmin dan developer yang bisa akses
        if (!in_array($user->role, ['superadmin', 'developer'])) {
            return response()->json([
                'results' => []
            ]);
        }
        
        $search = $request->get('search', '');

        $query = Opd::select('id', 'nama_penyelenggara', 'singkatan')
            ->orderBy('nama_penyelenggara', 'asc');

        // Filter by search term
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama_penyelenggara', 'like', "%{$search}%")
                  ->orWhere('singkatan', 'like', "%{$search}%");
            });
        }

        $opds = $query->get();

        $results = $opds->map(function($opd) {
            return [
                'id' => $opd->id,
                'text' => $opd->nama_penyelenggara . ($opd->singkatan ? ' (' . $opd->singkatan . ')' : '')
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    }
}
