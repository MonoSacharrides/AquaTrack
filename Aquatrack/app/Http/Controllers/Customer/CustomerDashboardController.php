<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Announcements;
use App\Models\MeterReading;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {

        $announcements = Announcements::count();

        $user = Auth::user();
        $customer_name = $user->name;
        // Get the latest 12 months of readings
        $readings = MeterReading::where('user_id', $user->id)
            ->orderBy('reading_date', 'desc')
            ->limit(12)
            ->get();

        // Calculate monthly usage stats
        $monthlyUsage = $readings->first()->reading ?? 0;
        $currentBill = $readings->first()->amount ?? 0;

        // Calculate yearly consumption
        $yearlyConsumption = $readings->sum('consumption');

        // Get area average (this would need to be implemented based on your business logic)
        $areaAverage = $this->calculateAreaAverage($user);

        // Prepare chart data
        $chartData = $readings->map(function ($reading) {
            return [
                'month' => $reading->billing_month,
                'consumption' => $reading->consumption,
                'reading' => $reading->reading,
            ];
        })->reverse()->values();



        return Inertia::render('Customer/Dashboard', [
            'customerName' => $customer_name,
            'announcements' => $announcements,
            'monthlyUsage' => $monthlyUsage,
            'currentBill' => $currentBill,
            'yearlyConsumption' => $yearlyConsumption,
            'areaAverage' => $areaAverage,
            'chartData' => $chartData,
        ]);
    }

    protected function calculateAreaAverage(User $user)
    {
        // This is a placeholder - implement your actual area average calculation
        // For example, you might average consumption for users in the same barangay
        return MeterReading::whereHas('user', function($query) use ($user) {
                $query->where('barangay', $user->barangay)
                    ->where('municipality', $user->municipality);
            })
            ->where('reading_date', '>=', now()->subYear())
            ->avg('consumption') ?? 0;
    }
}
