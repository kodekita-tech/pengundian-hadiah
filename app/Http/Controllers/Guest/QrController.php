<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QrController extends Controller
{
    /**
     * Show QR registration form.
     */
    public function show(string $token)
    {
        $event = Event::where('qr_token', $token)->with('opd')->firstOrFail();

        // Auto-update status based on date (without scheduler)
        $event->autoUpdateStatus();
        $event->refresh(); // Refresh to get updated status

        // Auto-update status berdasarkan tanggal
        $event->autoUpdateStatus();
        $event->refresh();

        // Check if event is active (within date range)
        // Status akan otomatis di-update oleh autoUpdateStatus()
        if ($event->status !== Event::STATUS_ACTIVE) {
            return view('guest.qr.closed', compact('event'));
        }

        // Generate captcha
        $captcha = $this->generateCaptcha();
        session(['captcha_answer' => $captcha['answer']]);

        return view('guest.qr.register', compact('event', 'token', 'captcha'));
    }

    /**
     * Refresh captcha (AJAX).
     */
    public function refreshCaptcha(string $token)
    {
        $event = Event::where('qr_token', $token)->firstOrFail();
        
        // Generate new captcha
        $captcha = $this->generateCaptcha();
        session(['captcha_answer' => $captcha['answer']]);

        return response()->json([
            'question' => $captcha['question']
        ]);
    }

    /**
     * Register participant via QR.
     */
    public function register(Request $request, string $token)
    {
        $event = Event::where('qr_token', $token)->firstOrFail();

        // Auto-update status berdasarkan tanggal
        $event->autoUpdateStatus();
        $event->refresh();

        // Check if event is active (within date range)
        // Status akan otomatis di-update oleh autoUpdateStatus()
        if ($event->status !== Event::STATUS_ACTIVE) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['event' => 'Pendaftaran untuk event ini belum dibuka atau sudah ditutup.']);
        }

        // Validate input first
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
            'asal' => ['required', 'string', 'max:255'],
            'captcha' => ['required', 'numeric'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.max' => 'Nomor HP maksimal 20 karakter.',
            'phone.unique' => 'Nomor HP ini sudah terdaftar untuk event ini.',
            'asal.required' => 'Asal wajib diisi.',
            'asal.max' => 'Asal maksimal 255 karakter.',
            'captcha.required' => 'Jawaban captcha wajib diisi.',
            'captcha.numeric' => 'Jawaban captcha harus berupa angka.',
        ]);

        // Validate captcha after basic validation
        $captchaAnswer = session('captcha_answer');
        if (!$captchaAnswer || (int)$request->captcha !== (int)$captchaAnswer) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['captcha' => 'Jawaban captcha salah. Silakan coba lagi.']);
        }

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
                'asal' => $validated['asal'],
                'is_winner' => false,
            ]);

            DB::commit();

            // Clear captcha after successful registration
            session()->forget('captcha_answer');

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
     * Generate captcha (simple math question).
     */
    private function generateCaptcha(): array
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $operator = rand(0, 1) ? '+' : '-';
        
        if ($operator === '+') {
            $answer = $num1 + $num2;
            $question = "$num1 + $num2 = ?";
        } else {
            // Ensure positive result
            if ($num1 < $num2) {
                $temp = $num1;
                $num1 = $num2;
                $num2 = $temp;
            }
            $answer = $num1 - $num2;
            $question = "$num1 - $num2 = ?";
        }

        return [
            'question' => $question,
            'answer' => $answer
        ];
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

