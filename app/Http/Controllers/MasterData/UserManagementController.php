<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\Permission;
use App\Models\Jabatan;
use App\Models\JabatanPermission;
use App\Models\UserPermission;
use App\Models\UserCompany;
use App\Models\Company;

class UserManagementController extends Controller
{
    // =============================================================================
    // USER MANAGEMENT METHODS
    // =============================================================================

    public function userIndex()
    {
        $search = request('search');
        $perPage = request('perPage', 10);
        $companycode = session('companycode'); // Get from session seperti di TenagaKerjaController
        
        $result = User::with(['jabatan', 'userCompanies'])
            ->when($search, function($query, $search) {
                return $query->where('userid', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('companycode', 'like', "%{$search}%")
                            ->orWhereHas('jabatan', function($q) use ($search) {
                                $q->where('namajabatan', 'like', "%{$search}%");
                            });
            })
            ->orderBy('createdat', 'desc')
            ->paginate($perPage);

        $jabatan = Jabatan::orderBy('namajabatan')->get();
        $companies = Company::orderBy('name')->get(); // Fixed: company.name bukan companyname
        
        return view('master.usermanagement.user.index', [
            'title' => 'User Management',
            'navbar' => 'User Management',
            'nav' => 'Users',
            'result' => $result,
            'jabatan' => $jabatan,
            'companies' => $companies,
            'perPage' => $perPage,
            'companycode' => $companycode
        ]);
    }

    public function userCreate()
    {
        $jabatan = Jabatan::orderBy('namajabatan')->get();
        $companies = Company::orderBy('name')->get();
        
        return view('master.usermanagement.user.create', [
            'title' => 'Create New User',
            'navbar' => 'User Management',
            'nav' => 'Create User',
            'jabatan' => $jabatan,
            'companies' => $companies
        ]);
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'userid' => 'required|string|max:50|unique:user,userid',
            'name' => 'required|string|max:30',
            'companycode' => 'required|string|max:4|exists:company,companycode',
            'idjabatan' => 'required|integer|exists:jabatan,idjabatan',
            'password' => 'required|string|min:6',
            'isactive' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'userid' => $request->userid,
                'name' => $request->name,
                'companycode' => $request->companycode,
                'idjabatan' => $request->idjabatan,
                'password' => Hash::make($request->password),
                'inputby' => Auth::user()->userid,
                'createdat' => now(),
                'isactive' => $request->isactive ?? 1,
            ]);

            // Auto-assign user to primary company
            UserCompany::create([
                'userid' => $request->userid,
                'companycode' => $request->companycode,
                'isactive' => 1,
                'grantedby' => Auth::user()->userid,
                'createdat' => now()
            ]);

            DB::commit();

            return redirect()->route('usermanagement.user.index')
                           ->with('success', 'User berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function userEdit($userid)
    {
        $user = User::with(['jabatan', 'userCompanies'])->find($userid);
        
        if (!$user) {
            return redirect()->route('usermanagement.user.index')
                           ->with('error', 'User tidak ditemukan');
        }

        $jabatan = Jabatan::orderBy('namajabatan')->get();
        $companies = Company::orderBy('name')->get();
        
        // Get effective permissions for display
        $effectivePermissions = $this->getUserEffectivePermissions($userid);
        
        return view('master.usermanagement.user.edit', [
            'title' => 'Edit User: ' . $user->name,
            'navbar' => 'User Management', 
            'nav' => 'Edit User',
            'user' => $user,
            'jabatan' => $jabatan,
            'companies' => $companies,
            'effectivePermissions' => $effectivePermissions
        ]);
    }

    public function userUpdate(Request $request, $userid)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'companycode' => 'required|string|max:4|exists:company,companycode',
            'idjabatan' => 'required|integer|exists:jabatan,idjabatan',
            'isactive' => 'boolean'
        ]);

        try {
            $user = User::find($userid);
            
            if (!$user) {
                return redirect()->route('usermanagement.user.index')
                               ->with('error', 'User tidak ditemukan');
            }

            $user->update([
                'name' => $request->name,
                'companycode' => $request->companycode,
                'idjabatan' => $request->idjabatan,
                'isactive' => $request->has('isactive') ? 1 : 0,
                'updatedat' => now()
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $request->validate(['password' => 'string|min:6']);
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            return redirect()->route('usermanagement.user.index')
                           ->with('success', 'User berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function userDestroy($userid)
    {
        try {
            $user = User::find($userid);
            
            if (!$user) {
                return redirect()->route('usermanagement.user.index')
                               ->with('error', 'User tidak ditemukan');
            }

            // Soft delete by setting isactive = 0
            $user->update([
                'isactive' => 0,
                'updatedat' => now()
            ]);

            return redirect()->route('usermanagement.user.index')
                           ->with('success', 'User berhasil dinonaktifkan');

        } catch (\Exception $e) {
            return redirect()->route('usermanagement.user.index')
                           ->with('error', 'Gagal menonaktifkan user: ' . $e->getMessage());
        }
    }

    // =============================================================================
    // PERMISSION MASTER DATA METHODS
    // =============================================================================

    public function permissionIndex()
    {
        $search = request('search');
        $perPage = request('perPage', 20);
        $categoryFilter = request('categories') ? explode(',', request('categories')) : [];
        
        $result = Permission::when($search, function($query, $search) {
                return $query->where('permissionname', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
            })
            ->when(!empty($categoryFilter), function($query) use ($categoryFilter) {
                return $query->whereIn('category', $categoryFilter);
            })
            ->orderBy('permissionid') // ✅ ADD THIS
            ->paginate($perPage);

        $categories = Permission::distinct()->pluck('category')->filter()->sort();
        
        return view('master.usermanagement.permissions-masterdata.index', [
            'title' => 'Permission Master Data',
            'navbar' => 'User Management',
            'nav' => 'Permissions',
            'result' => $result,
            'categories' => $categories,
            'perPage' => $perPage
        ]);
    }

    public function permissionStore(Request $request)
    {
        $request->validate([
            'permissionname' => 'required|string|max:100|unique:permissions,permissionname',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'isactive' => 'boolean'
        ]);

        try {
            Permission::create([
                'permissionname' => $request->permissionname,
                'category' => $request->category,
                'description' => $request->description,
                'isactive' => $request->has('isactive') ? 1 : 0
            ]);

            return redirect()->route('usermanagement.permission.index')
                           ->with('success', 'Permission berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan permission: ' . $e->getMessage());
        }
    }

    public function permissionUpdate(Request $request, $permissionid)
    {
        $request->validate([
            'permissionname' => 'required|string|max:100|unique:permissions,permissionname,' . $permissionid . ',permissionid',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'isactive' => 'boolean'
        ]);

        try {
            $permission = Permission::find($permissionid);
            
            if (!$permission) {
                return redirect()->route('usermanagement.permission.index')
                               ->with('error', 'Permission tidak ditemukan');
            }

            $permission->update([
                'permissionname' => $request->permissionname,
                'category' => $request->category,
                'description' => $request->description,
                'isactive' => $request->has('isactive') ? 1 : 0
            ]);

            return redirect()->route('usermanagement.permission.index')
                           ->with('success', 'Permission berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui permission: ' . $e->getMessage());
        }
    }

    public function permissionDestroy($permissionid)
    {
        try {
            $permission = Permission::find($permissionid);
            
            if (!$permission) {
                return redirect()->route('usermanagement.permission.index')
                               ->with('error', 'Permission tidak ditemukan');
            }

            // Check if permission is being used
            $usageCount = JabatanPermission::where('permissionid', $permissionid)->where('isactive', 1)->count();
            $userUsageCount = UserPermission::where('permissionid', $permissionid)->where('isactive', 1)->count();
            
            if ($usageCount > 0 || $userUsageCount > 0) {
                return redirect()->route('usermanagement.permission.index')
                               ->with('error', 'Permission sedang digunakan dan tidak bisa dihapus');
            }

            // Soft delete by setting isactive = 0
            $permission->update(['isactive' => 0]);

            return redirect()->route('usermanagement.permission.index')
                           ->with('success', 'Permission berhasil dinonaktifkan');

        } catch (\Exception $e) {
            return redirect()->route('usermanagement.permission.index')
                           ->with('error', 'Gagal menonaktifkan permission: ' . $e->getMessage());
        }
    }

    // =============================================================================
    // JABATAN PERMISSION METHODS
    // =============================================================================

    public function jabatanPermissionIndex()
    {
        $search = request('search');
        $perPage = request('perPage', 10);
        
        $result = Jabatan::withCount(['jabatanPermissions' => function($query) {
                $query->where('isactive', 1);
            }])
            ->when($search, function($query, $search) {
                return $query->where('namajabatan', 'like', "%{$search}%");
            })
            ->orderBy('namajabatan')
            ->paginate($perPage);

        $permissions = Permission::where('isactive', 1)
                                ->orderBy('category')
                                ->orderBy('permissionname')
                                ->get()
                                ->groupBy('category');
        
        return view('master.usermanagement.jabatan.index', [
            'title' => 'Jabatan Management',
            'navbar' => 'User Management',
            'nav' => 'Jabatan Permissions',
            'result' => $result,
            'permissions' => $permissions,
            'perPage' => $perPage
        ]);
    }

    public function jabatanPermissionStore(Request $request)
    // Fungsi ini juga untuk unchecked permission atau menghapuskan atau menonaktifkan ya. bukan hanya store.
    {
        $request->validate([
            'idjabatan' => 'required|integer|exists:jabatan,idjabatan',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,permissionid'
        ]);

        try {
            DB::beginTransaction();

            Log::info('Permission assignment request:', [
                'idjabatan' => $request->idjabatan,
                'permissions' => $request->permissions ?? []
            ]);

            // STEP 1: Nonaktifkan semua permissions untuk jabatan ini
            JabatanPermission::where('idjabatan', $request->idjabatan)
                            ->update(['isactive' => 0]); // Hapus updatedat

            // STEP 2: Aktifkan hanya permissions yang dipilih
            $selectedPermissions = $request->permissions ?? [];
            
            foreach ($selectedPermissions as $permissionid) {
                JabatanPermission::updateOrCreate([
                    'idjabatan' => $request->idjabatan,
                    'permissionid' => $permissionid
                ], [
                    'isactive' => 1,
                    'grantedby' => Auth::user()->userid,
                    'createdat' => now() // Hapus updatedat
                ]);
            }

            DB::commit();

            return redirect()->route('usermanagement.jabatan.index')
                        ->with('success', 'Permissions berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign permissions:', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                        ->with('error', 'Gagal memperbarui permissions: ' . $e->getMessage());
        }
    }

    /**
     * Store new jabatan
     */
    public function jabatanStore(Request $request)
    {
        $request->validate([
            'namajabatan' => 'required|string|max:30|unique:jabatan,namajabatan',
        ]);

        try {
            Jabatan::create([
                'namajabatan' => $request->namajabatan,
                'inputby' => Auth::user()->userid,
                'createdat' => now(),
            ]);

            return redirect()->route('usermanagement.jabatan.index')
                        ->with('success', 'Jabatan berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal menambahkan jabatan: ' . $e->getMessage());
        }
    }

    /**
     * Update jabatan
     */
    public function jabatanUpdate(Request $request, $idjabatan)
    {
        $request->validate([
            'namajabatan' => 'required|string|max:30|unique:jabatan,namajabatan,' . $idjabatan . ',idjabatan',
        ]);

        try {
            $jabatan = Jabatan::find($idjabatan);
            
            if (!$jabatan) {
                return redirect()->route('usermanagement.jabatan.index')
                            ->with('error', 'Jabatan tidak ditemukan');
            }

            $jabatan->update([
                'namajabatan' => $request->namajabatan,
                'updateby' => Auth::user()->userid,
                'updatedat' => now()
            ]);

            return redirect()->route('usermanagement.jabatan.index')
                        ->with('success', 'Jabatan berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal memperbarui jabatan: ' . $e->getMessage());
        }
    }

    /**
     * Delete jabatan (soft delete)
     */
    public function jabatanDestroy($idjabatan)
    {
        try {
            $jabatan = Jabatan::find($idjabatan);
            
            if (!$jabatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jabatan tidak ditemukan'
                ], 404);
            }

            // Check if jabatan is being used by any users
            $userCount = User::where('idjabatan', $idjabatan)->where('isactive', 1)->count();
            
            if ($userCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jabatan sedang digunakan oleh ' . $userCount . ' user dan tidak bisa dihapus'
                ], 422);
            }

            // Check if jabatan has any permissions
            $permissionCount = JabatanPermission::where('idjabatan', $idjabatan)
                                            ->where('isactive', 1)
                                            ->count();
            
            if ($permissionCount > 0) {
                // Deactivate all permissions for this jabatan first
                JabatanPermission::where('idjabatan', $idjabatan)
                                ->update(['isactive' => 0]);
            }

            // Delete the jabatan
            $jabatan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jabatan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete jabatan:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jabatan: ' . $e->getMessage()
            ], 500);
        }
    }

    // =============================================================================
    // USER COMPANY ACCESS METHODS
    // =============================================================================

    public function userCompanyIndex()
    {
        $search = request('search');
        $perPage = request('perPage', 15);
        
        // Group by user, aggregate companies
        $result = User::with(['jabatan', 'userCompanies' => function($query) {
                $query->where('isactive', 1);
            }, 'userCompanies.company'])
            ->whereHas('userCompanies', function($query) {
                $query->where('isactive', 1);
            })
            ->when($search, function($query, $search) {
                return $query->where('userid', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhereHas('userCompanies', function($q) use ($search) {
                                $q->where('companycode', 'like', "%{$search}%")
                                ->where('isactive', 1);
                            })
                            ->orWhereHas('userCompanies.company', function($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
            })
            ->orderBy('userid')
            ->paginate($perPage);

        // Get users who don't have any company access yet
        $users = User::with('jabatan')
            ->where('isactive', 1)
            ->whereDoesntHave('userCompanies', function($query) {
                $query->where('isactive', 1);
            })
            ->orderBy('name')
            ->get();
        
        $companies = Company::orderBy('name')->get();
        
        return view('master.usermanagement.user-company-permissions.index', [
            'title' => 'User Company Access',
            'navbar' => 'User Management',
            'nav' => 'Company Access',
            'result' => $result,
            'users' => $users,
            'companies' => $companies,
            'perPage' => $perPage
        ]);
    }

    public function userCompanyStore(Request $request)
    {
        $request->validate([
            'userid' => 'required|string|exists:user,userid',
            'companycodes' => 'required|array',
            'companycodes.*' => 'string|exists:company,companycode'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->companycodes as $companycode) {
                UserCompany::updateOrCreate([
                    'userid' => $request->userid,
                    'companycode' => $companycode
                ], [
                    'isactive' => 1,
                    'grantedby' => Auth::user()->userid,
                    'createdat' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('usermanagement.usercompany.index')
                           ->with('success', 'Company access berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->with('error', 'Gagal menambahkan company access: ' . $e->getMessage());
        }
    }

    public function userCompanyDestroy($userid, $companycode)
    {
        try {
            UserCompany::where('userid', $userid)
                       ->where('companycode', $companycode)
                       ->update([
                           'isactive' => 0,
                           'updatedat' => now()
                       ]);

            return redirect()->route('usermanagement.usercompany.index')
                           ->with('success', 'Company access berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('usermanagement.usercompany.index')
                           ->with('error', 'Gagal menghapus company access');
        }
    }

    public function userCompanyAssign(Request $request)
    {
        $request->validate([
            'userid' => 'required|string|exists:user,userid',
            'companycodes' => 'array', // Tidak required, bisa kosong (uncheck semua)
            'companycodes.*' => 'string|exists:company,companycode'
        ]);

        try {
            DB::beginTransaction();

            // STEP 1: Nonaktifkan semua company access untuk user ini
            UserCompany::where('userid', $request->userid)
                    ->update(['isactive' => 0]);

            // STEP 2: Aktifkan hanya companies yang dipilih
            $selectedCompanies = $request->companycodes ?? [];
            
            foreach ($selectedCompanies as $companycode) {
                UserCompany::updateOrCreate([
                    'userid' => $request->userid,
                    'companycode' => $companycode
                ], [
                    'isactive' => 1,
                    'grantedby' => Auth::user()->userid,
                    'createdat' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('usermanagement.usercompany.index')
                        ->with('success', 'Company access berhasil diperbarui untuk user');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                        ->with('error', 'Gagal memperbarui company access: ' . $e->getMessage());
        }
    }

    // =============================================================================
    // USER PERMISSION OVERRIDE METHODS
    // =============================================================================

    public function userPermissionIndex()
    {
        $search = request('search');
        $perPage = request('perPage', 15);
        
        $result = UserPermission::with(['user.jabatan', 'permissionModel'])
            ->when($search, function($query, $search) {
                return $query->where('userid', 'like', "%{$search}%")
                            ->orWhere('permission', 'like', "%{$search}%")
                            ->orWhere('companycode', 'like', "%{$search}%");
            })
            ->where('isactive', 1)
            ->orderBy('userid')
            ->orderBy('permission')
            ->paginate($perPage);

        $users = User::with('jabatan')->where('isactive', 1)->orderBy('name')->get();
        $permissions = Permission::where('isactive', 1)
                                ->orderBy('category')
                                ->orderBy('permissionname')
                                ->get()
                                ->groupBy('category');
        $companies = Company::orderBy('name')->get();
        
        return view('master.usermanagement.user-permissions.index', [
            'title' => 'User Permission Overrides',
            'navbar' => 'User Management',
            'nav' => 'Permission Overrides',
            'result' => $result,
            'users' => $users,
            'permissions' => $permissions,
            'companies' => $companies,
            'perPage' => $perPage
        ]);
    }

    public function userPermissionStore(Request $request)
    {
        $request->validate([
            'userid' => 'required|string|exists:user,userid',
            'companycode' => 'required|string|exists:company,companycode',
            'permissionid' => 'required|integer|exists:permissions,permissionid',
            'permissiontype' => 'required|in:GRANT,DENY',
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            // Check if user has access to the company
            $userCompany = UserCompany::where('userid', $request->userid)
                                     ->where('companycode', $request->companycode)
                                     ->where('isactive', 1)
                                     ->first();

            if (!$userCompany) {
                return redirect()->back()
                               ->with('error', 'User tidak memiliki akses ke company yang dipilih');
            }

            $permission = Permission::find($request->permissionid);

            UserPermission::updateOrCreate([
                'userid' => $request->userid,
                'companycode' => $request->companycode,
                'permission' => $permission->permissionname
            ], [
                'permissionid' => $request->permissionid,
                'permissiontype' => $request->permissiontype,
                'isactive' => 1,
                'reason' => $request->reason,
                'grantedby' => Auth::user()->userid,
                'createdat' => now()
            ]);

            return redirect()->route('usermanagement.userpermission.index')
                           ->with('success', 'Permission override berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan permission override: ' . $e->getMessage());
        }
    }

    public function userPermissionDestroy($userid, $companycode, $permission)
    {
        try {
            UserPermission::where('userid', $userid)
                         ->where('companycode', $companycode)
                         ->where('permission', $permission)
                         ->update([
                             'isactive' => 0,
                             'updatedat' => now()
                         ]);

            return redirect()->route('usermanagement.userpermission.index')
                           ->with('success', 'Permission override berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('usermanagement.userpermission.index')
                           ->with('error', 'Gagal menghapus permission override');
        }
    }

    // =============================================================================
    // UTILITY METHODS
    // =============================================================================

    public function getUserPermissionsSimple($userid)
    {
        try {
            $user = User::with('jabatan')->find($userid);
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $result = [
                'role' => null,
                'overrides' => []
            ];

            // Get role information
            if ($user->idjabatan && $user->jabatan) {
                $permissionCount = JabatanPermission::where('idjabatan', $user->idjabatan)
                                                ->where('isactive', 1)
                                                ->count();
                
                $result['role'] = [
                    'idjabatan' => $user->idjabatan,
                    'namajabatan' => $user->jabatan->namajabatan,
                    'count' => $permissionCount
                ];
            }

            // Get user-specific permission overrides
            $userPermissions = UserPermission::where('userid', $userid)
                                            ->where('isactive', 1)
                                            ->orderBy('permission')
                                            ->get();

            foreach ($userPermissions as $perm) {
                // Check if user has access to the company
                $hasCompanyAccess = UserCompany::where('userid', $userid)
                                            ->where('companycode', $perm->companycode)
                                            ->where('isactive', 1)
                                            ->exists();

                if ($hasCompanyAccess) {
                    $result['overrides'][] = [
                        'permission' => $perm->permission,
                        'companycode' => $perm->companycode,
                        'permissiontype' => $perm->permissiontype,
                        'reason' => $perm->reason,
                        'grantedby' => $perm->grantedby,
                        'createdat' => $perm->createdat ? $perm->createdat->format('Y-m-d H:i:s') : null
                    ];
                }
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error getting user permissions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load permissions'], 500);
        }
    }

    // API method for AJAX calls
    public function getJabatanPermissions($idjabatan)
    {
        $permissions = JabatanPermission::where('idjabatan', $idjabatan)
                                      ->where('isactive', 1)
                                      ->with('permission')
                                      ->get();

        return response()->json([
            'permissions' => $permissions
        ]);
    }

    // Testing method
    public function testUserPermission($userid, $permission)
    {
        $user = User::find($userid);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Use the CheckPermission middleware method
        $middleware = new \App\Http\Middleware\CheckPermission();
        $hasPermission = method_exists($middleware, 'checkUserPermission') 
                        ? $middleware->checkUserPermission($user, $permission)
                        : false;

        return response()->json([
            'user' => $user->userid,
            'permission' => $permission,
            'has_permission' => $hasPermission,
            'effective_permissions' => $this->getUserEffectivePermissions($userid)
        ]);
    }
}