<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Get roles for Select2 dropdown
     */
    public function getRoles(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['results' => []], 400);
        }

            $search = $request->get('search', '');
            $roles = [
                ['id' => 'superadmin', 'text' => 'Super Admin'],
                ['id' => 'developer', 'text' => 'Developer'],
                ['id' => 'admin_opd', 'text' => 'Admin OPD'],
            ];

            // Filter berdasarkan search jika ada
            if (!empty($search)) {
                $roles = array_filter($roles, function($role) use ($search) {
                    return stripos($role['text'], $search) !== false;
                });
            $roles = array_values($roles);
        }

        return response()->json(['results' => $roles]);
    }

    /**
     * Get OPDs for Select2 dropdown
     */
    public function getOpds(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['results' => []], 400);
        }

        $search = $request->get('search', '');
        $opds = Opd::select('id', 'nama_instansi')
            ->when($search, function($query) use ($search) {
                return $query->where('nama_instansi', 'like', '%' . $search . '%')
                             ->orWhere('singkatan', 'like', '%' . $search . '%');
            })
            ->orderBy('nama_instansi')
            ->get()
            ->map(function($opd) {
                return [
                    'id' => $opd->id,
                    'text' => $opd->nama_instansi
                ];
            });

        return response()->json(['results' => $opds]);
        }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([], 400);
        }

        $users = User::with('opd')
            ->select('id', 'name', 'email', 'role', 'opd_id', 'created_at')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user) {
                $opdId = $user->opd_id ? $user->opd_id : '';
                $opdName = $user->opd ? htmlspecialchars($user->opd->nama_instansi, ENT_QUOTES, 'UTF-8') : '';
                
                    $btn = '<div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-primary btn-edit" 
                                data-id="' . $user->id . '" 
                                data-role="' . htmlspecialchars($user->role, ENT_QUOTES, 'UTF-8') . '"
                                data-opd-id="' . $opdId . '"
                                data-opd-name="' . $opdName . '"
                                data-bs-toggle="modal" 
                                data-bs-target="#userModal" 
                                title="Edit">
                                    <i class="fi fi-rr-edit"></i>
                                </button>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" 
                                data-id="' . $user->id . '" 
                                title="Delete">
                                    <i class="fi fi-rr-trash"></i>
                                </button>
                            </div>';
                    return $btn;
                })
                ->editColumn('role', function ($user) {
                    $badgeClass = match($user->role) {
                        'superadmin' => 'bg-danger',
                        'developer' => 'bg-primary',
                        'admin_opd' => 'bg-success',
                        default => 'bg-secondary'
                    };
                    $roleText = ucfirst(str_replace('_', ' ', $user->role));
                    return '<span class="badge ' . $badgeClass . '">' . $roleText . '</span>';
                })
            ->addColumn('opd', function ($user) {
                return $user->opd ? $user->opd->nama_instansi : '-';
            })
                ->editColumn('created_at', function ($user) {
                    return $user->created_at->format('d M Y');
                })
                ->rawColumns(['action', 'role'])
                ->make(true);
        }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
                'opd_id' => $request->opd_id,
        ]);

            DB::commit();

        return response()->json([
            'success' => true,
                'message' => 'User berhasil dibuat.',
                'data' => $user->load('opd')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat user: ' . $e->getMessage()
            ], 500);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = User::with('opd')->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            DB::beginTransaction();

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
                'opd_id' => $request->opd_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

            DB::commit();

        return response()->json([
            'success' => true,
                'message' => 'User berhasil diperbarui.',
                'data' => $user->load('opd')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
        $user = User::findOrFail($id);
            
            // Prevent deleting own account
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun sendiri.'
                ], 422);
            }
            
            DB::beginTransaction();
        $user->delete();
            DB::commit();

        return response()->json([
            'success' => true,
                'message' => 'User berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
    }
    }

    /**
     * Download template for importing users
     */
    public function downloadTemplate()
    {
        try {
            $opds = Opd::select('nama_instansi')
                ->orderBy('nama_instansi')
                ->pluck('nama_instansi')
                ->toArray();
        
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Data Users
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Data Users');
        
        // Set headers
        $sheet1->setCellValue('A1', 'name');
        $sheet1->setCellValue('B1', 'email');
        $sheet1->setCellValue('C1', 'password');
        $sheet1->setCellValue('D1', 'role');
        $sheet1->setCellValue('E1', 'opd');
        
        // Set example data
        $sheet1->setCellValue('A2', 'John Doe');
        $sheet1->setCellValue('B2', 'john.doe@example.com');
        $sheet1->setCellValue('C2', 'password123');
        $sheet1->setCellValue('D2', 'admin_opd');
        $sheet1->setCellValue('E2', !empty($opds) ? $opds[0] : '');
        
        $sheet1->setCellValue('A3', 'Jane Smith');
        $sheet1->setCellValue('B3', 'jane.smith@example.com');
        $sheet1->setCellValue('C3', 'password123');
        $sheet1->setCellValue('D3', 'admin_opd');
        $sheet1->setCellValue('E3', !empty($opds) && count($opds) > 1 ? $opds[1] : (!empty($opds) ? $opds[0] : ''));
        
        // Data validation for role (only admin_opd)
        $roleValidation = $sheet1->getCell('D2')->getDataValidation();
        $roleValidation->setType(DataValidation::TYPE_LIST);
        $roleValidation->setFormula1('"admin_opd"');
        $roleValidation->setShowDropDown(true);
        $roleValidation->setAllowBlank(false);
        $sheet1->setDataValidation('D2:D1000', $roleValidation);
        
        // Sheet 2: Daftar OPD
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Daftar OPD');
        $sheet2->setCellValue('A1', 'nama_instansi');
        
        $row = 2;
        foreach ($opds as $opd) {
            $sheet2->setCellValue('A' . $row, $opd);
            $row++;
        }
        
        // Calculate last row for OPD list
        $lastRow = $row > 2 ? ($row - 1) : 2;
        
        // Data validation for OPD (dropdown from Sheet2)
        $opdValidation = $sheet1->getCell('E2')->getDataValidation();
        $opdValidation->setType(DataValidation::TYPE_LIST);
        $opdValidation->setFormula1('\'Daftar OPD\'!$A$2:$A$' . $lastRow);
        $opdValidation->setShowDropDown(true);
        $opdValidation->setAllowBlank(true);
        $opdValidation->setShowErrorMessage(true);
        $opdValidation->setErrorTitle('Invalid OPD');
        $opdValidation->setError('Please select OPD from the dropdown list.');
        $sheet1->setDataValidation('E2:E1000', $opdValidation);
        
        // Auto size columns
        foreach (range('A', 'E') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet2->getColumnDimension('A')->setAutoSize(true);
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_users.xlsx';
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
     * Import users from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
            'default_opd_id' => 'nullable|exists:opd,id',
        ]);

        try {
            $file = $request->file('file');
            $defaultOpdId = $request->default_opd_id;

            // Read only first sheet (Data Users)
            $data = Excel::toArray(new class implements ToArray, WithHeadingRow {
                public function array(array $array): array
                {
                    return $array;
                }
            }, $file);

            $imported = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($data[0] as $index => $row) {
                $rowNumber = $index + 2; // +2 karena ada header dan index mulai dari 0

                try {
                    // Get OPD ID from exact match nama_instansi
                    $opdId = null;
                    if (!empty($row['opd'])) {
                        $opdName = trim($row['opd']);
                        // Try exact match first
                        $opd = Opd::where('nama_instansi', $opdName)->first();
                        if (!$opd) {
                            // Try with singkatan
                            $opd = Opd::where('singkatan', $opdName)->first();
                        }
                        if ($opd) {
                            $opdId = $opd->id;
                        } else {
                            $errors[] = "Baris {$rowNumber}: OPD '{$opdName}' tidak ditemukan. Gunakan nama instansi yang tepat dari daftar OPD.";
                            continue;
                        }
                    }
                    
                    // Use default OPD if not found in row and default is provided
                    if (!$opdId && $defaultOpdId) {
                        $opdId = $defaultOpdId;
                    }

                    // Get role from row - must be admin_opd
                    $role = !empty($row['role']) ? trim($row['role']) : 'admin_opd';
                    
                    // Validate role - only admin_opd allowed
                    if ($role !== 'admin_opd') {
                        $errors[] = "Baris {$rowNumber}: Role '{$role}' tidak valid. Role harus 'admin_opd'.";
                        continue;
                    }

                    // Validate required fields
                    if (empty($row['name']) || empty($row['email']) || empty($row['password'])) {
                        $errors[] = "Baris {$rowNumber}: Nama, email, dan password wajib diisi.";
                        continue;
                    }

                    if (strlen($row['password']) < 8) {
                        $errors[] = "Baris {$rowNumber}: Password minimal 8 karakter.";
                        continue;
                    }

                    if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Baris {$rowNumber}: Format email tidak valid.";
                        continue;
                    }

                    User::updateOrCreate(
                        ['email' => $row['email']],
                        [
                            'name' => $row['name'],
                            'password' => Hash::make($row['password']),
                            'role' => $role,
                            'opd_id' => $opdId,
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Berhasil mengimpor {$imported} user.";
            if (count($errors) > 0) {
                $message .= " Terjadi " . count($errors) . " error.";
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
}
