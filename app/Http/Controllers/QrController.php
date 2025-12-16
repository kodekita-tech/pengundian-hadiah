<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QrController extends Controller
{
    /**
     * Show QR registration form.
     */
    public function show(string $token)
    {
        $event = Event::where('qr_token', $token)->with('opd')->firstOrFail();

        // Check if event is open for registration
        // Allow registration if status is pendaftaran_dibuka OR if within date range (more flexible)
        $isWithinDateRange = $event->tgl_mulai <= now() && $event->tgl_selesai >= now();
        $isRegistrationOpen = $event->status === Event::STATUS_REGISTRATION_OPEN;
        
        // If status is draft but within date range, allow registration (flexible)
        // If status is registration_open, always allow if within date range
        if (!$isRegistrationOpen && !($event->status === Event::STATUS_DRAFT && $isWithinDateRange)) {
            return view('guest.qr.closed', compact('event'));
        }

        // Check date range
        if (!$isWithinDateRange) {
            return view('guest.qr.closed', compact('event'));
        }

        return view('guest.qr.register', compact('event', 'token'));
    }

    /**
     * Register participant via QR.
     */
    public function register(Request $request, string $token)
    {
        $event = Event::where('qr_token', $token)->firstOrFail();

        // Check if event is open for registration (more flexible)
        $isWithinDateRange = $event->tgl_mulai <= now() && $event->tgl_selesai >= now();
        $isRegistrationOpen = $event->status === Event::STATUS_REGISTRATION_OPEN;
        
        if (!$isRegistrationOpen && !($event->status === Event::STATUS_DRAFT && $isWithinDateRange)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['event' => 'Pendaftaran untuk event ini belum dibuka atau sudah ditutup.']);
        }

        if (!$isWithinDateRange) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['event' => 'Pendaftaran untuk event ini belum dibuka atau sudah ditutup.']);
        }

        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:20',
                \Illuminate\Validation\Rule::unique('participants', 'phone')
                    ->where(function ($query) use ($event) {
                        return $query->where('event_id', $event->id);
                    })
            ],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.max' => 'Nomor HP maksimal 20 karakter.',
            'phone.unique' => 'Nomor HP ini sudah terdaftar untuk event ini.',
        ]);

        try {
            DB::beginTransaction();

            // Generate unique coupon number
            $couponNumber = $this->generateCouponNumber($event->id);

            // Create participant
            $participant = Participant::create([
                'event_id' => $event->id,
                'coupon_number' => $couponNumber,
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'is_winner' => false,
            ]);

            DB::commit();

            return view('guest.qr.success', compact('event', 'participant', 'token'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal mendaftar. Silakan coba lagi.']);
        }
    }

    /**
     * Generate unique coupon number for event.
     */
    private function generateCouponNumber(int $eventId): string
    {
        do {
            // Generate 6-digit coupon number
            $couponNumber = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (Participant::where('event_id', $eventId)
            ->where('coupon_number', $couponNumber)
            ->exists());

        return $couponNumber;
    }
}

