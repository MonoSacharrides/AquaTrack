<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MeterReading;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class StaffDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Staff/Dashboard');
    }

    public function getDashboardData()
    {
        try {
            // Get today's date
            $today = Carbon::today();
            $startOfWeek = Carbon::now()->startOfWeek();
            $startOfMonth = Carbon::now()->startOfMonth();
            $staffUser = Auth::user();
            $staffName = $staffUser ? $staffUser->name : 'Staff';

            // Today's readings count (all readings for today)
            $todaysReadings = MeterReading::whereDate('reading_date', $today)->count();

            // Weekly readings count
            $weeklyReadings = MeterReading::whereBetween('reading_date', [$startOfWeek, $today])->count();

            // Monthly readings count
            $monthlyReadings = MeterReading::whereBetween('reading_date', [$startOfMonth, $today])->count();

            // Recent activity (last 5 readings)
            $recentActivity = MeterReading::with(['user' => function ($query) {
                $query->select('id', 'name', 'lastname', 'account_number', 'serial_number');
            }])
                ->orderBy('reading_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($reading) {
                    return [
                        'id' => $reading->id,
                        'account_number' => $reading->user->account_number ?? 'N/A',
                        'customer_name' => ($reading->user->name ?? 'Unknown') . ' ' . ($reading->user->lastname ?? ''),
                        'serial_number' => $reading->user->serial_number ?? 'N/A',
                        'reading' => $reading->reading,
                        'consumption' => $reading->consumption,
                        'reading_date' => $reading->reading_date->format('Y-m-d'),
                        'reading_time' => $reading->created_at->format('H:i A'),
                        'status' => $reading->status,
                        'is_high_consumption' => $reading->consumption > 20,
                        'type' => 'reading'
                    ];
                });

            // Completed readings today
            $completedToday = MeterReading::whereDate('reading_date', $today)->count();

            // Pending readings - Safe approach
            $customersWithReadingsToday = MeterReading::whereDate('reading_date', $today)
                ->pluck('user_id')
                ->unique()
                ->toArray();

            $pendingCount = User::where('role', 'customer')
                ->where('enabled', true)
                ->whereNotIn('id', $customersWithReadingsToday)
                ->count();

            // Status counts for all readings
            $statusCounts = [
                'paid' => MeterReading::where('status', 'Paid')->count(),
                'pending' => MeterReading::where('status', 'Pending')->count(),
                'overdue' => MeterReading::where('status', 'Overdue')->count(),
            ];

            return response()->json([
                'staffName' => $staffName,
                'todaysReadings' => $todaysReadings,
                'weeklyReadings' => $weeklyReadings,
                'monthlyReadings' => $monthlyReadings,
                'recentActivity' => $recentActivity,
                'completedToday' => $completedToday,
                'pendingCount' => $pendingCount,
                'statusCounts' => $statusCounts
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard data error: ' . $e->getMessage());

            return response()->json([
                'todaysReadings' => 0,
                'weeklyReadings' => 0,
                'monthlyReadings' => 0,
                'recentActivity' => [],
                'completedToday' => 0,
                'pendingCount' => 0,
                'statusCounts' => [
                    'paid' => 0,
                    'pending' => 0,
                    'overdue' => 0,
                ],
                'error' => 'Failed to load dashboard data'
            ], 500);
        }
    }
}
