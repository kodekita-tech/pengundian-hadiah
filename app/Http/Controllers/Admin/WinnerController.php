<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Winner;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WinnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of winners.
     */
    public function index(Request $request)
    {
        $query = Winner::with(['event', 'participant', 'prize'])
            ->orderBy('drawn_at', 'desc');

        $user = $request->user();
        
        // Filter by event if provided
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by opd_id only for admin_penyelenggara role
        if ($user && $user->role === 'admin_penyelenggara' && $user->opd_id) {
            $query->whereHas('event', function($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            });
        }

        $winners = $query->paginate(20);
        
        // Get events for filter dropdown
        $eventsQuery = Event::orderBy('nm_event', 'asc');
        if ($user && $user->role === 'admin_penyelenggara' && $user->opd_id) {
            $eventsQuery->where('opd_id', $user->opd_id);
        }
        $events = $eventsQuery->get();

        return view('admin.winner.index', compact('winners', 'events'));
    }

    /**
     * Remove the specified winner.
     */
    public function destroy($id)
    {
        $winner = Winner::with(['event', 'participant'])->findOrFail($id);
        
        // Check authorization for admin_penyelenggara
        $user = Auth::user();
        if ($user && $user->role === 'admin_penyelenggara' && $user->opd_id) {
            if ($winner->event && $winner->event->opd_id !== $user->opd_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
        }

        // Update participant is_winner status
        if ($winner->participant) {
            $winner->participant->update(['is_winner' => false]);
        }

        $winner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pemenang berhasil dihapus.'
        ]);
    }
}

