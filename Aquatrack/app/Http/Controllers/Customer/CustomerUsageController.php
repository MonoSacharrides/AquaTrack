<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MeterReading;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;



class CustomerUsageController extends Controller
{
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Fetch meter readings for the authenticated user
        $readings = MeterReading::where('user_id', $user->id)
            ->orderBy('reading_date', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($reading) {
                return [
                    'id' => $reading->id, // Add ID for updates
                    'month' => $reading->billing_month . ' ' . $reading->reading_date->format('Y'),
                    'usage' => $reading->consumption,
                    'amount' => number_format($reading->amount, 2),
                    'status' => $reading->status ?? ($reading->amount > 0 ? 'Pending' : 'Paid'), // Use actual status if available
                ];
            });


        // Prepare chart data
        $chartData = MeterReading::where('user_id', $user->id)
            ->orderBy('reading_date', 'asc')
            ->limit(6)
            ->get()
            ->map(function ($reading) {
                return [
                    'month' => $reading->billing_month,
                    'usage' => $reading->consumption,
                    'amount' => $reading->amount,
                ];
            });

        return Inertia::render('Customer/Usage', [
            'usageData' => $readings,
            'chartData' => $chartData,
        ]);
    }


    public function getPreviousReadings($userId)
    {
        try {
            $user = User::where('id', $userId)
                ->where('role', 'customer')
                ->firstOrFail();

            $readings = MeterReading::where('user_id', $userId)
                ->orderBy('reading_date', 'desc')
                ->limit(12)
                ->get()
                ->map(function ($reading) {
                    return [
                        'billing_month' => $reading->billing_month,
                        'reading_date' => $reading->reading_date ? $reading->reading_date->format('Y-m-d') : 'N/A',
                        'previous_reading' => $reading->previous_reading, // Include previous reading
                        'reading' => $reading->reading,
                        'consumption' => $reading->consumption,
                        'amount' => $reading->amount,
                        'year' => $reading->reading_date ? $reading->reading_date->format('Y') : date('Y')
                    ];
                });

            return response()->json($readings);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching previous readings: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Update the status of a meter reading (mark as paid)
     */
    public function update(Request $request, $id)
    {
        // Find the meter reading
        $reading = MeterReading::findOrFail($id);

        // Authorization check - only admins can update status
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Validate the request
        $validated = $request->validate([
            'status' => 'required|in:Pending,Paid'
        ]);

        // Update the status
        $reading->status = $validated['status'];
        $reading->save();

        return response()->json([
            'message' => 'Bill status updated successfully!',
            'reading' => [
                'id' => $reading->id,
                'month' => $reading->billing_month . ' ' . $reading->reading_date->format('Y'),
                'usage' => $reading->consumption,
                'amount' => number_format($reading->amount, 2),
                'status' => $reading->status,
            ]
        ]);
    }
}
