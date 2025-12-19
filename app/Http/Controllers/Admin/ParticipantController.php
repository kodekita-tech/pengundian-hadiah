<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ParticipantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the participants for an event.
     */
    public function index(Event $event)
    {
        return view('admin.event.participants', compact('event'));
    }

    /**
     * Get data for DataTables
     */
    public function getData(Event $event, Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([], 400);
        }

        $participants = Participant::where('event_id', $event->id)
            ->select('id', 'coupon_number', 'name', 'phone', 'asal', 'is_winner')
            ->orderBy('id', 'desc');

        return DataTables::of($participants)
            ->addIndexColumn()
            ->addColumn('action', function ($participant) {
                $btn = '<div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-danger btn-delete" 
                                data-id="' . $participant->id . '" 
                                title="Delete">
                                <i class="fi fi-rr-trash"></i>
                            </button>
                        </div>';
                return $btn;
            })
            ->editColumn('asal', function ($participant) {
                return $participant->asal ?? '-';
            })
            ->editColumn('is_winner', function ($participant) {
                if ($participant->is_winner) {
                    return '<span class="badge bg-success">Winner</span>';
                }
                return '<span class="badge bg-secondary">Participant</span>';
            })
            ->rawColumns(['action', 'is_winner'])
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $participant = Participant::findOrFail($id);
            
            DB::beginTransaction();
            $participant->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Peserta berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus peserta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download template for importing participants
     */
    public function downloadTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Peserta');
            
            // Set headers
            $sheet->setCellValue('A1', 'nomor_kupon');
            $sheet->setCellValue('B1', 'nama');
            $sheet->setCellValue('C1', 'no_hp');
            
            // Set example data
            $sheet->setCellValue('A2', '0001');
            $sheet->setCellValue('B2', 'Budi Santoso');
            $sheet->setCellValue('C2', '081234567890');
            
            $sheet->setCellValue('A3', '0002');
            $sheet->setCellValue('B3', 'Siti Aminah');
            $sheet->setCellValue('C3', '089876543210');
            
            // Auto size columns
            foreach (range('A', 'C') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $writer = new Xlsx($spreadsheet);
            $filename = 'template_import_peserta.xlsx';
            $filePath = storage_path('app/temp/' . $filename);
            
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            $writer->save($filePath);
            
            return response()->download($filePath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import participants from Excel file
     */
    public function import(Event $event, Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120', // Max 5MB
        ]);

        try {
            $file = $request->file('file');

            // Read first sheet
            $data = Excel::toArray(new class implements ToArray, WithHeadingRow {
                public function array(array $array): array
                {
                    return $array;
                }
            }, $file);

            $imported = 0;
            $errors = [];
            
            // Headers mapping (flexible headers)
            // nomor_kupon / coupon_number / no_kupon
            // nama / name
            // no_hp / phone / telephone

            DB::beginTransaction();

            $existingCoupons = Participant::where('event_id', $event->id)
                ->pluck('coupon_number')
                ->toArray();
            
            // Convert to hash map for faster lookup if massive
            $existingCouponsMap = array_flip($existingCoupons);

            foreach ($data[0] as $index => $row) {
                $rowNumber = $index + 2;

                try {
                    // Normalize keys to lower case (Maatwebsite usually handles this with slug, but let's be safe)
                    // The keys usually come from header row processed by WithHeadingRow (slugged)
                    
                    $coupon = $row['nomor_kupon'] ?? $row['coupon_number'] ?? $row['no_kupon'] ?? null;
                    $name = $row['nama'] ?? $row['name'] ?? null;
                    $phone = $row['no_hp'] ?? $row['phone'] ?? $row['telephone'] ?? null;

                    if (empty($coupon) || empty($name)) {
                        $errors[] = "Baris {$rowNumber}: Nomor Kupon dan Nama wajib diisi.";
                        continue;
                    }

                    // Convert coupon to string
                    $coupon = (string) $coupon;

                    // Check duplicate in current import batch is hard, but we rely on DB check or loop check?
                    // Let's check against DB map
                    if (isset($existingCouponsMap[$coupon])) {
                        $errors[] = "Baris {$rowNumber}: Nomor Kupon '{$coupon}' sudah ada di event ini.";
                        continue;
                    }

                    Participant::create([
                        'event_id' => $event->id,
                        'coupon_number' => $coupon,
                        'name' => $name,
                        'phone' => $phone,
                        'is_winner' => false,
                    ]);

                    // Add to map to prevent dupes within same file from passing
                    $existingCouponsMap[$coupon] = true;

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Berhasil mengimpor {$imported} peserta.";
            if (count($errors) > 0) {
                $message .= " Terjadi " . count($errors) . " error (lihat detail di console/log).";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export participants to Excel
     */
    public function export(Event $event)
    {
        try {
            $participants = Participant::where('event_id', $event->id)
                ->orderBy('id', 'desc')
                ->get();

            // Get all winners for this event to map prize information
            $winners = Winner::where('event_id', $event->id)
                ->pluck('prize_name', 'participant_id')
                ->toArray();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Peserta');

            // Set headers with styling
            $headers = ['No', 'No. Kupon', 'Nama', 'No. HP', 'Status', 'Hadiah yang Diterima', 'Tanggal Daftar'];
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
            foreach ($participants as $index => $participant) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $participant->coupon_number);
                $sheet->setCellValue('C' . $row, $participant->name);
                $sheet->setCellValue('D' . $row, $participant->phone ?? '-');
                $sheet->setCellValue('E' . $row, $participant->is_winner ? 'Pemenang' : 'Peserta');
                
                // Get prize name if participant is a winner
                $prizeName = isset($winners[$participant->id]) ? $winners[$participant->id] : '-';
                $sheet->setCellValue('F' . $row, $prizeName);
                
                $sheet->setCellValue('G' . $row, $participant->created_at->format('d M Y H:i'));
                $row++;
            }

            // Auto size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Set borders
            $sheet->getStyle('A1:G' . ($row - 1))->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $writer = new Xlsx($spreadsheet);
            $filename = 'peserta_' . str_replace([' ', '/'], '_', $event->nm_event) . '_' . date('Ymd_His') . '.xlsx';
            $filePath = storage_path('app/temp/' . $filename);

            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $writer->save($filePath);

            return response()->download($filePath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear all participants for an event
     */
    public function clear(Event $event)
    {
        try {
            DB::beginTransaction();
            $event->participants()->delete(); // Model Event hasMany Participant
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Semua data peserta berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
