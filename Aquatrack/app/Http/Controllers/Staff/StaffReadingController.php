<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MeterReading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Carbon\Carbon;

class StaffReadingController extends Controller
{
    public function index()
    {
        return Inertia::render('Staff/Reading');
    }

    public function search(Request $request)
    {
        try {
            $query = trim($request->input('query', ''));

            if (empty($query) || strlen($query) < 2) {
                return response()->json([]);
            }

            $users = User::where('role', 'customer')
                ->where(function ($q) use ($query) {
                    // Search by name (first or last name)
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('lastname', 'like', "%{$query}%");

                    // Search by serial number
                    $q->orWhere('serial_number', 'like', "%{$query}%");
                })
                ->select([
                    'id',
                    'name',
                    'lastname',
                    'account_number',
                    'zone',
                    'barangay',
                    'municipality',
                    'province',
                    'phone',
                    'date_installed',
                    'brand',
                    'serial_number',
                    'size',
                    'avatar',
                ])
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'lastname' => $user->lastname,
                        'account_number' => $user->account_number,
                        'address' => implode(', ', array_filter([
                            $user->zone,
                            $user->barangay,
                            $user->municipality,
                            $user->province
                        ])),
                        'phone' => $user->phone,
                        'date_installed' => $user->date_installed,
                        'brand' => $user->brand,
                        'serial_number' => $user->serial_number,
                        'size' => $user->size,
                        'avatar_url' => $user->avatar ? Storage::url($user->avatar) : null
                    ];
                });

            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function getUserDetails($userId)
    {
        $user = User::where('id', $userId)
            ->where('role', 'customer')
            ->firstOrFail();

        return response()->json([
            'name' => $user->name,
            'lastname' => $user->lastname,
            'account_number' => $user->account_number,
            'address' => implode(', ', array_filter([
                $user->zone,
                $user->barangay,
                $user->municipality,
                $user->province
            ])),
            'phone' => $user->phone,
            'date_installed' => $user->date_installed ?? 'Not available',
            'brand' => $user->brand ?? 'Not specified',
            'serial_number' => $user->serial_number ?? 'N/A',
            'size' => $user->size ?? 'N/A'
        ]);
    }

    public function getPreviousReadings($userId)
    {
        try {
            // Validate the user exists and is a customer
            $user = User::where('id', $userId)
                ->where('role', 'customer')
                ->firstOrFail();

            $readings = MeterReading::where('user_id', $userId)
                ->orderBy('reading_date', 'desc')
                ->limit(12)
                ->get()
                ->map(function ($reading) {
                    return [
                        'id' => $reading->id,
                        'billing_month' => $reading->billing_month,
                        'reading_date' => $reading->reading_date ? Carbon::parse($reading->reading_date)->format('Y-m-d') : 'N/A',
                        'reading' => $reading->reading,
                        'previous_reading' => $reading->previous_reading,
                        'consumption' => $reading->consumption,
                        'amount' => $reading->amount,
                        'status' => $reading->status,
                        'year' => $reading->reading_date ? Carbon::parse($reading->reading_date)->format('Y') : date('Y')
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

    public function updateReading(Request $request, $readingId)
    {
        try {
            $validated = $request->validate([
                'reading' => 'required|numeric|min:0',
                'amount' => 'required|numeric|min:0',
                'status' => 'required|in:Pending,Paid',
                'consumption' => 'required|numeric|min:0',
            ]);

            $reading = MeterReading::findOrFail($readingId);

            // Check if staff has permission to edit this reading
            if ($reading->staff_id !== Auth::id()) {
                return response()->json([
                    'error' => 'You are not authorized to edit this reading'
                ], 403);
            }

            $reading->update([
                'reading' => $validated['reading'],
                'amount' => $validated['amount'],
                'status' => $validated['status'],
                'consumption' => $validated['consumption'],
                'updated_at' => now(),
            ]);

            // Refresh the reading to get updated data
            $reading->refresh();

            return response()->json([
                'message' => 'Reading updated successfully',
                'reading' => [
                    'id' => $reading->id,
                    'billing_month' => $reading->billing_month,
                    'reading_date' => $reading->reading_date ? Carbon::parse($reading->reading_date)->format('Y-m-d') : 'N/A',
                    'reading' => $reading->reading,
                    'previous_reading' => $reading->previous_reading,
                    'consumption' => $reading->consumption,
                    'amount' => $reading->amount,
                    'status' => $reading->status,
                    'year' => $reading->reading_date ? Carbon::parse($reading->reading_date)->format('Y') : date('Y')
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Reading not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating reading: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function storeReading(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'billing_month' => 'required|string',
            'reading_date' => 'required|date',
            'reading' => 'required|numeric|min:0',
            'previous_reading' => 'nullable|numeric|min:0',
        ]);

        // Parse the reading date to get year
        $readingDate = Carbon::parse($validated['reading_date']);
        $readingYear = $readingDate->format('Y');

        // Check if reading already exists for this month and year
        $existingReading = MeterReading::where('user_id', $validated['user_id'])
            ->where('billing_month', $validated['billing_month'])
            ->whereYear('reading_date', $readingYear)
            ->first();

        if ($existingReading) {
            return response()->json([
                'error' => 'A reading already exists for this billing month and year'
            ], 422);
        }

        // Fetch the user to get the zone
        $user = User::findOrFail($validated['user_id']);

        // Determine the correct previous reading
        $previousReadingValue = $this->determinePreviousReading(
            $validated['user_id'],
            $validated['previous_reading'] ?? null,
            $readingDate
        );

        // Validate that current reading is greater than previous reading
        if ($validated['reading'] <= $previousReadingValue) {
            return response()->json([
                'error' => 'Current reading must be greater than previous reading'
            ], 422);
        }

        $consumption = $validated['reading'] - $previousReadingValue;
        $amount = $this->calculateBillAmount($consumption);

        // Calculate due date based on user's zone and reading date
        $dueDate = $this->getDueDate($user, $validated['reading_date']);

        $newReading = MeterReading::create([
            'user_id' => $validated['user_id'],
            'staff_id' => Auth::id(),
            'billing_month' => $validated['billing_month'],
            'reading_date' => $validated['reading_date'],
            'previous_reading' => $previousReadingValue,
            'reading' => $validated['reading'],
            'consumption' => $consumption,
            'amount' => $amount,
            'status' => 'Pending',
            'due_date' => $dueDate,
            'created_at' => now(), // Ensure accurate timestamp
            'updated_at' => now(), // Ensure accurate timestamp
        ]);

        return response()->json([
            'message' => 'Reading saved successfully',
            'reading' => $newReading
        ]);
    }

    /**
     * Determine the correct previous reading value
     */
    private function determinePreviousReading($userId, $providedPreviousReading, $currentReadingDate)
    {
        // If previous reading is provided, use it
        if ($providedPreviousReading !== null) {
            return (float) $providedPreviousReading;
        }

        // Get the most recent reading before the current reading date
        $previousReading = MeterReading::where('user_id', $userId)
            ->where('reading_date', '<', $currentReadingDate->format('Y-m-d'))
            ->orderBy('reading_date', 'desc')
            ->first();

        if ($previousReading) {
            return $previousReading->reading;
        }

        // If no previous reading found, this might be the first reading
        return 0;
    }

    /**
     * Calculate bill amount based on tiered pricing
     * 1-10 m³ = ₱132 (fixed)
     * 11-20 m³ = ₱14 per m³
     * 21-30 m³ = ₱14.85 per m³
     * 31-40 m³ = ₱16 per m³
     * 41+ m³ = ₱17.25 per m³
     */
    private function calculateBillAmount($consumption)
    {
        if ($consumption <= 0) {
            return 0;
        }

        if ($consumption <= 10) {
            return 132.00; // Fixed rate for first 10 m³
        }

        $amount = 132.00; // Base amount for first 10 m³

        if ($consumption > 10 && $consumption <= 20) {
            $amount += ($consumption - 10) * 14;
        } elseif ($consumption > 20 && $consumption <= 30) {
            $amount += (10 * 14) + (($consumption - 20) * 14.85);
        } elseif ($consumption > 30 && $consumption <= 40) {
            $amount += (10 * 14) + (10 * 14.85) + (($consumption - 30) * 16);
        } else {
            $amount += (10 * 14) + (10 * 14.85) + (10 * 16) + (($consumption - 40) * 17.25);
        }

        return round($amount, 2);
    }

    /**
     * Get the due date based on the user's zone and reading date.
     */
    private function getDueDate($user, $readingDate)
    {
        // Extract numeric zone, default to 1 if invalid
        $zone = $user->zone ? (int) preg_replace('/[^0-9]/', '', $user->zone) : 1;
        if ($zone < 1 || $zone > 12) {
            $zone = 1; // Fallback to Zone 1
        }

        // Define due dates strictly per zone rules
        $dueDayMap = [
            1 => 15,  // Zone 1: 15th
            2 => 16,  // Zone 2: 16th
            3 => 16,  // Zone 3: 16th
            4 => 17,  // Zone 4: 17th
            5 => 18,  // Zone 5: 18th
            6 => 19,  // Zone 6: 19th
            7 => 19,  // Zone 7: 19th
            8 => 20,  // Zone 8: 20th
            9 => 21,  // Zone 9: 21st
            10 => 22, // Zone 10: 22nd
            11 => 23, // Zone 11: 23rd
            12 => 23, // Zone 12: 23rd
        ];

        $dueDay = $dueDayMap[$zone] ?? 15; // Default to 15 if zone not found
        $readingDate = Carbon::parse($readingDate);

        // Create due date in the SAME MONTH as the reading date
        $dueDate = $readingDate->copy()->day($dueDay);

        // If the reading date is after the due day of the current month, set to next month's due day
        if ($readingDate->day > $dueDay) {
            $dueDate = $dueDate->addMonth();
        }

        // Adjust for weekends (move to next working day)
        while ($dueDate->isWeekend()) {
            $dueDate = $dueDate->addDay();
        }

        return $dueDate->toDateString();
    }
}
