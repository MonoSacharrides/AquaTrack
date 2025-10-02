<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Report;
use App\Models\User;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get current year and month for analytics
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Fetch dashboard data
        $dashboardData = [
            'total_users' => User::count(),
            'total_staffs' => User::role('staff')->count(),
            'total_reports' => Report::count(),
            'total_customers' => User::role('customer')->count(),
            'monthly_consumption' => $this->getMonthlyConsumption($currentYear),
            'current_month_consumption' => $this->getCurrentMonthConsumption($currentYear, $currentMonth),
            'average_consumption' => $this->getAverageConsumption($currentYear),
            'peak_usage' => $this->getPeakUsage($currentYear),
            'growth_percentage' => $this->getGrowthPercentage($currentYear, $currentMonth),
            'resolution_rate' => $this->getReportResolutionRate(),
            'recent_activities' => $this->getRecentActivities(),
        ];

        return Inertia::render('Admin/Dashboard', $dashboardData);
    }

    /**
     * Export water analytics data as CSV.
     */
    public function exportWaterAnalytics(Request $request)
    {
        $data = $request->validate([
            'monthly_consumption' => 'required|array',
            'current_month_consumption' => 'required|numeric',
            'average_consumption' => 'required|numeric',
            'peak_usage' => 'required|numeric',
        ]);

        $filename = "water_analytics_{Carbon::now()->format('Ymd_His')}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            // Write CSV headers
            fputcsv($file, [
                'Month',
                'Monthly Consumption (m³)',
                'Current Month Consumption (m³)',
                'Average Consumption (m³)',
                'Peak Usage (m³)',
            ]);

            // Write data for each month
            foreach ($data['monthly_consumption'] as $index => $consumption) {
                fputcsv($file, [
                    $months[$index],
                    round($consumption, 2),
                    round($data['current_month_consumption'], 2),
                    round($data['average_consumption'], 2),
                    round($data['peak_usage'], 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get monthly consumption data for the specified year.
     */
    private function getMonthlyConsumption($year)
    {
        $consumptionData = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $totalConsumption = MeterReading::whereBetween('reading_date', [$startDate, $endDate])
                ->sum('consumption');

            $consumptionData[] = round($totalConsumption, 2);
        }

        return $consumptionData;
    }

    /**
     * Get current month's consumption.
     */
    private function getCurrentMonthConsumption($year, $month)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        return MeterReading::whereBetween('reading_date', [$startDate, $endDate])
            ->sum('consumption');
    }

    /**
     * Get average consumption for the year.
     */
    private function getAverageConsumption($year)
    {
        $startDate = Carbon::create($year, 1, 1)->startOfYear();
        $endDate = Carbon::create($year, 12, 31)->endOfYear();

        $totalConsumption = MeterReading::whereBetween('reading_date', [$startDate, $endDate])
            ->sum('consumption');

        $monthCount = Carbon::now()->month; // Months that have passed this year

        return $monthCount > 0 ? round($totalConsumption / $monthCount, 2) : 0;
    }

    /**
     * Get peak usage for the year.
     */
    private function getPeakUsage($year)
    {
        $peakUsage = 0;

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $monthlyConsumption = MeterReading::whereBetween('reading_date', [$startDate, $endDate])
                ->sum('consumption');

            if ($monthlyConsumption > $peakUsage) {
                $peakUsage = $monthlyConsumption;
            }
        }

        return round($peakUsage, 2);
    }

    /**
     * Get growth percentage compared to previous month.
     */
    private function getGrowthPercentage($year, $month)
    {
        if ($month == 1) {
            return 0; // No previous month in January
        }

        // Current month consumption
        $currentStartDate = Carbon::create($year, $month, 1)->startOfMonth();
        $currentEndDate = Carbon::create($year, $month, 1)->endOfMonth();
        $currentConsumption = MeterReading::whereBetween('reading_date', [$currentStartDate, $currentEndDate])
            ->sum('consumption');

        // Previous month consumption
        $prevMonth = $month - 1;
        $prevStartDate = Carbon::create($year, $prevMonth, 1)->startOfMonth();
        $prevEndDate = Carbon::create($year, $prevMonth, 1)->endOfMonth();
        $prevConsumption = MeterReading::whereBetween('reading_date', [$prevStartDate, $prevEndDate])
            ->sum('consumption');

        if ($prevConsumption == 0) {
            return $currentConsumption > 0 ? 100 : 0;
        }

        return round((($currentConsumption - $prevConsumption) / $prevConsumption) * 100, 1);
    }

    /**
     * Get report resolution rate.
     */
    private function getReportResolutionRate()
    {
        $totalReports = Report::count();
        $resolvedReports = Report::where('status', 'Resolved')->count();

        return $totalReports > 0 ? round(($resolvedReports / $totalReports) * 100, 0) : 0;
    }

    /**
     * Get recent activities with formatted data.
     */
    private function getRecentActivities()
    {
        return Activity::latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'event' => $activity->event,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at,
                    'causer_name' => $activity->causer?->name ?? 'System',
                    'properties' => $activity->properties,
                    'subject_type' => $activity->subject_type,
                ];
            });
    }

    /**
     * Get activity title based on event.
     */
    protected function getActivityTitle($activity): string
    {
        return match ($activity->event) {
            'created' => "New {$activity->log_name} created",
            'updated' => "{$activity->log_name} updated",
            'deleted' => "{$activity->log_name} deleted",
            'logged_in' => "User logged in",
            'logged_out' => "User logged out",
            default => "System activity",
        };
    }

    /**
     * Get activity icon based on event.
     */
    protected function getActivityIcon($activity): string
    {
        return match ($activity->event) {
            'created' => 'bi-file-earmark-plus',
            'updated' => 'bi-pencil-square',
            'deleted' => 'bi-trash',
            'logged_in' => 'bi-box-arrow-in-right',
            'logged_out' => 'bi-box-arrow-left',
            default => 'bi-activity',
        };
    }

    /**
     * Get activity color based on event.
     */
    protected function getActivityColor($activity): string
    {
        return match ($activity->event) {
            'created' => 'blue',
            'updated' => 'green',
            'deleted' => 'red',
            'logged_in' => 'purple',
            'logged_out' => 'gray',
            default => 'indigo',
        };
    }
}
