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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            ->select('id', 'coupon_number', 'name', 'phone', 'asal')
            ->inRandomOrder() 
            ->get();

        return response()->json($candidates);
    }

    /**
     * API: Get all coupon numbers for visual random effect
     */
    public function getAllCouponNumbers($shortlink)
    {
        $event = Event::where('shortlink', $shortlink)->firstOrFail();
        
        $couponNumbers = Participant::where('event_id', $event->id)
            ->where('is_winner', false)
            ->pluck('coupon_number')
            ->toArray();

        return response()->json($couponNumbers);
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

    /**
     * API: Store multiple winners at once
     */
    public function storeWinners(Request $request, $shortlink)
    {
        $event = Event::where('shortlink', $shortlink)->firstOrFail();
        
        $request->validate([
            'winners' => 'required|array|min:1',
            'winners.*.participant_id' => 'required|exists:participants,id',
            'winners.*.prize_id' => 'required|exists:prizes,id',
        ]);

        try {
            DB::beginTransaction();

            $savedWinners = [];
            $errors = [];

            foreach ($request->winners as $index => $winnerData) {
                try {
                    $participant = Participant::where('id', $winnerData['participant_id'])
                        ->where('event_id', $event->id)
                        ->lockForUpdate()
                        ->firstOrFail();
                    
                    $prize = Prize::where('id', $winnerData['prize_id'])
                        ->where('event_id', $event->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($participant->is_winner) {
                        throw new \Exception("Peserta #{$index} sudah menang sebelumnya.");
                    }

                    if (!$prize->isAvailable()) {
                        throw new \Exception("Stok hadiah untuk peserta #{$index} sudah habis.");
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

                    $savedWinners[] = $winner->load(['participant', 'prize']);

                } catch (\Exception $e) {
                    $errors[] = "Pemenang #{$index}: " . $e->getMessage();
                }
            }

            if (count($errors) > 0 && count($savedWinners) === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan pemenang.',
                    'errors' => $errors
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($savedWinners) . ' pemenang berhasil disimpan.',
                'winners' => $savedWinners,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Export winners to Excel
     */
    public function exportWinners($shortlink)
    {
        try {
            $event = Event::where('shortlink', $shortlink)->firstOrFail();
            
            $winners = Winner::with(['participant', 'prize'])
                ->where('event_id', $event->id)
                ->orderBy('drawn_at', 'desc')
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Pemenang');

            // Set headers with styling
            $headers = ['No', 'No. Kupon', 'Nama', 'No. HP', 'Hadiah', 'Tanggal Diundi'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->getFont()->setBold(true);
                $sheet->getStyle($col . '1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF4472C4');
                $sheet->getStyle($col . '1')->getFont()->getColor()->setARGB('FFFFFFFF');
                $col++;
            }

            // Set data
            $row = 2;
            foreach ($winners as $index => $winner) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $winner->participant->coupon_number ?? '-');
                $sheet->setCellValue('C' . $row, $winner->participant->name ?? '-');
                $sheet->setCellValue('D' . $row, $winner->participant->phone ?? '-');
                $sheet->setCellValue('E' . $row, $winner->prize_name ?? '-');
                $sheet->setCellValue('F' . $row, $winner->drawn_at ? $winner->drawn_at->format('d M Y H:i') : '-');
                $row++;
            }

            // Auto size columns
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Set borders
            if ($row > 2) {
                $sheet->getStyle('A1:F' . ($row - 1))->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'pemenang_' . str_replace([' ', '/'], '_', $event->nm_event) . '_' . date('Ymd_His') . '.xlsx';
            $filePath = storage_path('app/temp/' . $filename);

            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $writer->save($filePath);

            return response()->download($filePath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}

