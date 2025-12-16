<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OpdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.opd.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $opd = Opd::select('id', 'nama_instansi', 'singkatan', 'nomor_hp', 'created_at')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');

            return DataTables::of($opd)
                ->addIndexColumn()
                ->addColumn('action', function ($opd) {
                    $btn = '<div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-primary btn-edit" data-id="' . $opd->id . '" data-bs-toggle="modal" data-bs-target="#opdModal" title="Edit">
                                    <i class="fi fi-rr-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $opd->id . '" title="Delete">
                                    <i class="fi fi-rr-trash"></i>
                                </button>
                            </div>';
                    return $btn;
                })
                ->editColumn('created_at', function ($opd) {
                    return $opd->created_at->format('d M Y');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_instansi' => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:100',
            'nomor_hp' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $opd = Opd::create([
            'nama_instansi' => $request->nama_instansi,
            'singkatan' => $request->singkatan,
            'nomor_hp' => $request->nomor_hp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OPD created successfully',
            'data' => $opd
        ]);
    }

    public function show($id)
    {
        $opd = Opd::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $opd
        ]);
    }

    public function update(Request $request, $id)
    {
        $opd = Opd::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_instansi' => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:100',
            'nomor_hp' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $opd->update([
            'nama_instansi' => $request->nama_instansi,
            'singkatan' => $request->singkatan,
            'nomor_hp' => $request->nomor_hp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OPD updated successfully',
            'data' => $opd
        ]);
    }

    public function destroy($id)
    {
        $opd = Opd::findOrFail($id);
        $opd->delete();

        return response()->json([
            'success' => true,
            'message' => 'OPD deleted successfully'
        ]);
    }

    public function downloadTemplate()
    {
        $data = [
            [
                'nama_instansi' => 'Dinas Komunikasi dan Informatika',
                'singkatan' => 'Diskominfo',
                'nomor_hp' => '081234567890',
            ],
            [
                'nama_instansi' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',
                'singkatan' => 'DP3AP2KB',
                'nomor_hp' => '081234567891',
            ],
        ];

        return Excel::download(new class($data) implements FromArray, WithHeadings, ShouldAutoSize {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'nama_instansi',
                    'singkatan',
                    'nomor_hp',
                ];
            }
        }, 'template_import_opd.xlsx');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $data = Excel::toArray(new class implements ToArray, WithHeadingRow {
                public function array(array $array): array
                {
                    return $array;
                }
            }, $file);

            $imported = 0;
            $errors = [];

            foreach ($data[0] as $index => $row) {
                $rowNumber = $index + 2; // +2 karena ada header dan index mulai dari 0

                try {
                    $validator = Validator::make($row, [
                        'nama_instansi' => 'required|string|max:255',
                        'singkatan' => 'nullable|string|max:100',
                        'nomor_hp' => 'nullable|string|max:20',
                    ]);

                    if ($validator->fails()) {
                        $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                        continue;
                    }

                    Opd::updateOrCreate(
                        ['nama_instansi' => $row['nama_instansi']],
                        [
                            'singkatan' => $row['singkatan'] ?? null,
                            'nomor_hp' => $row['nomor_hp'] ?? null,
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} OPD(s)";
            if (count($errors) > 0) {
                $message .= ". " . count($errors) . " error(s) occurred.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
