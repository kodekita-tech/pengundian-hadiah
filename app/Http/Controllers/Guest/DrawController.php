<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Winner;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Prize;

class DrawController extends Controller
{
    /**
     * Show the draw page or passkey verification page.
     */
    public function show(string $shortlink)
    {
        $event = Event::where('shortlink', $shortlink)->with('opd')->firstOrFail();
        
        // Auto-update status based on date (without scheduler)
        $event->autoUpdateStatus();
        $event->refresh(); // Refresh to get updated status
        
        // Check if event has passkey protection
        if ($event->hasPasskey()) {
            // Check if already verified in session
            $sessionKey = 'event_' . $event->id . '_verified';
            if (!Session::get($sessionKey, false)) {
                return view('guest.draw.passkey', compact('event'));
            }
        }

        // Load prizes
        $prizes = $event->prizes()->get();

        // Load existing winners for display
        $winners = Winner::with(['participant', 'prize'])
            ->where('event_id', $event->id)
            ->orderBy('drawn_at', 'desc')
            ->get();
        
        return view('guest.draw.index', compact('event', 'winners', 'prizes'));
    }

    /**
     * Verify passkey and grant access to draw page.
     */
    public function verifyPasskey(Request $request, string $shortlink)
    {
        $event = Event::where('shortlink', $shortlink)->firstOrFail();
        
        $request->validate([
            'passkey' => ['required', 'string']
        ], [
            'passkey.required' => 'Passkey wajib diisi.'
        ]);
        
        if ($event->verifyPasskey($request->passkey)) {
            // Store verification in session
            $sessionKey = 'event_' . $event->id . '_verified';
            Session::put($sessionKey, true);
            
            return redirect()->route('draw.show', $shortlink);
        }
        
        return back()->withErrors([
            'passkey' => 'Passkey salah. Silakan coba lagi.'
        ])->withInput();
    }

    /**
     * API: Get list of eligible candidates (participants who haven't won)
     */
    public function getCandidates($shortlink)
    {
        $event = Event::where('shortlink', $shortlink)->firstOrFail();
        
        $candidates = Participant::where('event_id', $event->id)
            ->where('is_winner', false)
            ->select('id', 'coupon_number', 'name', 'phone')
            ->inRandomOrder() 
            ->get();

        return response()->json($candidates);
    }

    /**
     * API: Store a new winner
     */
    public function storeWinner(Request $request, $shortlink)
    {
        $event = Event::where('shortlink', $shortlink)->firstOrFail();
        
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'prize_id' => 'required|exists:prizes,id',
        ]);

        try {
            DB::beginTransaction();

            $participant = Participant::where('id', $request->participant_id)
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->firstOrFail();
            
            $prize = Prize::where('id', $request->prize_id)
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($participant->is_winner) {
                throw new \Exception('Peserta ini sudah menang sebelumnya.');
            }

            if (!$prize->isAvailable()) {
                throw new \Exception('Stok hadiah ini sudah habis.');
            }

            // Mark participant as winner
            $participant->is_winner = true;
            $participant->save();

            // Decrement prize stock
            $prize->decrementStock();

            // Create winner record
            $winner = Winner::create([
                'event_id' => $event->id,
                'participant_id' => $participant->id,
                'prize_id' => $prize->id,
                'prize_name' => $prize->name,
                'drawn_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemenang berhasil disimpan.',
                'winner' => $winner->load(['participant', 'prize']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}

