<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminUsersController extends Controller
{
    public function getDashboardStats()
    {
        try {
            // Active Users (enabled users)
            $activeUsers = User::where('enabled', true)->count();

            // Total Users
            $totalUsers = User::count();

            // Resolved Issues
            $resolvedIssues = Report::where('status', 'resolved')->count();

            // Total Reports
            $totalReports = Report::count();

            // Calculate resolution percentage (avoid division by zero)
            $resolutionPercentage = $totalReports > 0
                ? round(($resolvedIssues / $totalReports) * 100, 1)
                : 0;

            return [
                'active_users' => $activeUsers,
                'total_users' => $totalUsers,
                'resolved_issues' => $resolvedIssues,
                'total_reports' => $totalReports,
                'resolution_percentage' => $resolutionPercentage,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard stats: ' . $e->getMessage());
            return [
                'active_users' => 0,
                'total_users' => 0,
                'resolved_issues' => 0,
                'total_reports' => 0,
                'resolution_percentage' => 0,
            ];
        }
    }

    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function ($query, $role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->when($request->filled('enabled'), function ($query) use ($request) {
                if ($request->enabled === '0' || $request->enabled === 0 || $request->enabled === false || $request->enabled === 'false') {
                    $query->where('enabled', false);
                } elseif ($request->enabled === '1' || $request->enabled === 1 || $request->enabled === true || $request->enabled === 'true') {
                    $query->where('enabled', true);
                }
            })
            ->orderBy($request->sort ?? 'id', $request->order ?? 'desc')
            ->paginate($request->per_page ?? 10)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'account_number' => $user->account_number,
                    'avatar_url' => $user->avatar_url,
                    'role' => $user->roles->first()?->name ?? 'none',
                    'zone' => $user->zone,
                    'barangay' => $user->barangay,
                    'date_installed' => $user->date_installed,
                    'brand' => $user->brand,
                    'serial_number' => $user->serial_number,
                    'size' => $user->size,
                    'enabled' => $user->enabled,
                ];
            });

        // Define zones for the frontend
        $zones = [
            "Zone 1" => ["Poblacion Sur"],
            "Zone 2" => ["Poblacion Centro"],
            "Zone 3" => ["Poblacion Centro"],
            "Zone 4" => ["Poblacion Norte"],
            "Zone 5" => ["Candajec", "Buangan"],
            "Zone 6" => ["Bonbon"],
            "Zone 7" => ["Bonbon"],
            "Zone 8" => ["Nahawan"],
            "Zone 9" => ["Caboy", "Villaflor", "Cantuyoc"],
            "Zone 10" => ["Bacani", "Mataub", "Comaang", "Tangaran"],
            "Zone 11" => ["Cantuyoc", "Nahawan"],
            "Zone 12" => ["Lajog", "Buacao"],
        ];

        // FIXED: Get dashboard statistics before passing to view
        $dashboardStats = $this->getDashboardStats();

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'sort', 'order', 'per_page', 'enabled', 'action']),
            'zones' => $zones,
            'dashboardStats' => $dashboardStats, // FIXED: Now properly defined
        ]);
    }

    // AdminUsersController.php - Update the store method validation
    public function store(Request $request)
    {
        // Convert empty strings to null for ALL optional fields
        $request->merge([
            'account_number' => $request->account_number === '' ? null : $request->account_number,
            'zone' => $request->zone === '' ? null : $request->zone,
            'barangay' => $request->barangay === '' ? null : $request->barangay,
            'date_installed' => $request->date_installed === '' ? null : $request->date_installed,
            'brand' => $request->brand === '' ? null : $request->brand,
            'serial_number' => $request->serial_number === '' ? null : $request->serial_number,
            'size' => $request->size === '' ? null : $request->size,
            'email' => $request->email === '' ? null : $request->email,
        ]);

        // For staff, ensure account_number is completely omitted if null
        if ($request->role === 'staff') {
            $request->request->remove('account_number');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => $request->role === 'staff' ? 'required|string|email|max:255' : 'nullable|string|email|max:255',
            'phone' => [
                'required',
                'string',
                'regex:/^(\+63\d{10}|09\d{9})$/',
            ],
            'role' => 'required|in:customer,staff,admin',
            'account_number' => $request->role === 'customer' ? [
                'required',
                'string',
                'regex:/^\d{3}-\d{2}-\d{3}$/',
                'unique:users',
            ] : 'nullable|string|regex:/^\d{3}-\d{2}-\d{3}$/',
            'zone' => $request->role === 'customer' ? 'required|string' : 'nullable|string',
            'barangay' => $request->role === 'customer' ? 'required|string' : 'nullable|string',
            'date_installed' => $request->role === 'customer' ? 'required|date' : 'nullable|date',
            'brand' => $request->role === 'customer' ? 'required|string|max:255' : 'nullable|string|max:255',
            'serial_number' => $request->role === 'customer' ? 'required|string|size:9|regex:/^\d{9}$/|unique:users' : 'nullable|string|size:9|regex:/^\d{9}$/',
            'size' => $request->role === 'customer' ? 'required|string|max:50' : 'nullable|string|max:50',
            'enabled' => 'boolean',
        ]);

        // Debug: Check what values are being sent
        Log::info('Creating user with data:', $validated);

        $userData = [
            'name' => $validated['name'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'zone' => $validated['zone'] ?? null,
            'barangay' => $validated['barangay'] ?? null,
            'date_installed' => $validated['date_installed'] ?? null,
            'brand' => $validated['brand'] ?? null,
            'serial_number' => $validated['serial_number'] ?? null,
            'size' => $validated['size'] ?? null,
            'password' => Hash::make('temporary_password'),
            'enabled' => true,
        ];

        // Only include account_number if it's not null (for customers)
        if (isset($validated['account_number']) && $validated['account_number'] !== null) {
            $userData['account_number'] = $validated['account_number'];
        }

        $user = User::create($userData);

        // Generate password based on role-specific logic
        if ($validated['role'] === 'customer' && isset($validated['account_number'])) {
            $password = strtoupper(substr($validated['lastname'], 0, 3)) . '_' . substr(preg_replace('/\D/', '', $validated['account_number']), 0, 3);
        } else {
            // For staff, use phone number for password generation
            $password = strtoupper(substr($validated['lastname'], 0, 3)) . '_' . "STAFF";
        }

        $user->update(['password' => Hash::make($password)]);
        $user->assignRole($validated['role']);

        return redirect()->route('admin.users')->with([
            'success' => 'User created successfully',
            'generated_password' => $password,
        ]);
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6'
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->back()->with([
            'success' => 'Password reset successfully'
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'phone' => [
                'required',
                'string',
                'regex:/^(\+63\d{10}|09\d{9})$/',
            ],

            'zone' => 'required|string',
            'barangay' => 'required|string',
            'date_installed' => 'required|date',
            'brand' => 'required|string|max:255',
            'serial_number' => 'required|string|size:9|regex:/^\d{9}$/|unique:users,serial_number,' . $user->id,
            'size' => 'required|string|max:50',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'User updated successfully');
    }

    public function toggleStatus(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'enabled' => 'required|boolean',
        ]);

        $userIds = $validated['user_ids'];
        $enabled = $validated['enabled'];

        // Update user status
        User::whereIn('id', $userIds)->update(['enabled' => $enabled]);

        // Simple: Delete sessions for deactivated users
        if (!$enabled) {
            foreach ($userIds as $userId) {
                // This will force logout on next request
                DB::table('sessions')
                    ->where('user_id', $userId)
                    ->delete();
            }
        }

        // Re-fetch the full paginated data with the current filters
        $users = User::with('roles')
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                        ->orWhere('phone', 'like', "%{$request->search}%")
                        ->orWhere('lastname', 'like', "%{$request->search}%")
                        ->orWhere('serial_number', 'like', "%{$request->search}%");
                });
            })
            ->when($request->role, function ($query) use ($request) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('name', $request->role);
                });
            })
            ->when($request->filled('enabled'), function ($query) use ($request) {
                if (in_array($request->enabled, ['0', 'false', 0, false])) {
                    $query->where('enabled', false);
                } elseif (in_array($request->enabled, ['1', 'true', 1, true])) {
                    $query->where('enabled', true);
                }
            })
            ->orderBy($request->sort ?? 'id', $request->order ?? 'desc')
            ->paginate($request->per_page ?? 10)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar_url' => $user->avatar_url,
                    'role' => $user->roles->first()?->name ?? 'none',
                    'zone' => $user->zone,
                    'barangay' => $user->barangay,
                    'date_installed' => $user->date_installed,
                    'brand' => $user->brand,
                    'serial_number' => $user->serial_number,
                    'size' => $user->size,
                    'enabled' => $user->enabled,
                ];
            });

        // Define zones for the frontend
        $zones = [
            "Zone 1" => ["Poblacion Sur"],
            "Zone 2" => ["Poblacion Centro"],
            "Zone 3" => ["Poblacion Centro"],
            "Zone 4" => ["Poblacion Norte"],
            "Zone 5" => ["Candajec", "Buangan"],
            "Zone 6" => ["Bonbon"],
            "Zone 7" => ["Bonbon"],
            "Zone 8" => ["Nahawan"],
            "Zone 9" => ["Caboy", "Villaflor", "Cantuyoc"],
            "Zone 10" => ["Bacani", "Mataub", "Comaang", "Tangaran"],
            "Zone 11" => ["Cantuyoc", "Nahawan"],
            "Zone 12" => ["Lajog", "Buacao"],
        ];

        // FIXED: Get updated dashboard statistics
        $dashboardStats = $this->getDashboardStats();

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'sort', 'order', 'per_page', 'enabled', 'action']),
            'zones' => $zones,
            'dashboardStats' => $dashboardStats, // FIXED: Added dashboard stats
            'flash' => ['success' => 'User status updated successfully'],
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with([
            'success' => 'User deleted successfully'
        ]);
    }

    // API endpoint for stats
    public function getStats()
    {
        try {
            $stats = $this->getDashboardStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'data' => $this->getDashboardStats() // Return fallback data
            ], 500);
        }
    }
}
