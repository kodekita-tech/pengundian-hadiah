<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.users.index');
    }

    public function getRoles(Request $request)
    {
        if ($request->ajax()) {
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
                $roles = array_values($roles); // Re-index array
            }

            return response()->json([
                'results' => $roles
            ]);
        }
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select('id', 'name', 'email', 'role', 'created_at')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user) {
                    $btn = '<div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-primary btn-edit" data-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#userModal" title="Edit">
                                    <i class="fi fi-rr-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $user->id . '" title="Delete">
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
                ->editColumn('created_at', function ($user) {
                    return $user->created_at->format('d M Y');
                })
                ->rawColumns(['action', 'role'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:superadmin,developer,admin_opd',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:superadmin,developer,admin_opd',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function downloadTemplate()
    {
        $opds = Opd::select('nama_instansi')->orderBy('nama_instansi')->pluck('nama_instansi')->toArray();
        
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
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls|max:2048',
            'default_opd_id' => 'nullable|exists:opd,id',
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
                            $errors[] = "Row {$rowNumber}: OPD '{$opdName}' not found. Please use exact nama_instansi from OPD list.";
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
                        $errors[] = "Row {$rowNumber}: Invalid role '{$role}'. Role must be 'admin_opd'";
                        continue;
                    }

                    $validator = Validator::make($row, [
                        'name' => 'required|string|max:255',
                        'email' => 'required|string|email|max:255',
                        'password' => 'required|string|min:8',
                    ]);

                    if ($validator->fails()) {
                        $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
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
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} user(s)";
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
