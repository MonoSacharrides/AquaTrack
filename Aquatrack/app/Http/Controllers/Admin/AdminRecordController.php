<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminRecordController extends Controller
{
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
            1 => 15, // Zone 1: 15th
            2 => 16, // Zone 2: 16th
            3 => 16, // Zone 3: 16th
            4 => 17, // Zone 4: 17th
            5 => 18, // Zone 5: 18th
            6 => 19, // Zone 6: 19th
            7 => 19, // Zone 7: 19th
            8 => 20, // Zone 8: 20th
            9 => 21, // Zone 9: 21st
            10 => 22, // Zone 10: 22nd
            11 => 23, // Zone 11: 23rd
            12 => 23, // Zone 12: 23rd
        ];

        $dueDay = $dueDayMap[$zone] ?? 15; // Default to 15 if zone not found
        $readingDate = Carbon::parse($readingDate);
        $dueDate = $readingDate->copy()->day($dueDay);

        // If the reading date is after the due day of the current month, set to next month's due day
        if ($readingDate->day > $dueDay || ($readingDate->day == $dueDay && $readingDate->hour >= 0)) {
            $dueDate = $dueDate->addMonth();
        }

        // Adjust for weekends (move to next working day)
        while ($dueDate->isWeekend()) {
            $dueDate = $dueDate->addDay();
        }

        return $dueDate->toDateString();
    }

    /**
     * Calculate surcharge if the record is overdue.
     */
    private function calculateSurcharge($record, $dueDate)
    {
        if ($record->status === 'Paid' || !$dueDate) {
            return null;
        }

        $dueDate = Carbon::parse($dueDate);
        $today = Carbon::today('America/Los_Angeles'); // Adjusted for PST

        // Surcharge applies the day after the due date
        if ($today->gt($dueDate)) {
            return round($record->amount * 0.10, 2); // 10% surcharge
        }

        return null;
    }

    public function index(Request $request)
    {
        // Build the query with relationships
        $query = MeterReading::with('user');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Apply month filter
        if ($request->has('month') && !empty($request->month)) {
            $query->whereMonth('reading_date', $request->month);
        }

        // Apply year filter
        if ($request->has('year') && !empty($request->year)) {
            $query->whereYear('reading_date', $request->year);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        if ($sortField === 'name') {
            $query->join('users', 'meter_readings.user_id', '=', 'users.id')
                ->orderBy('users.name', $sortDirection)
                ->orderBy('users.lastname', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Get paginated results
        $perPage = $request->get('perPage', 10);
        $records = $query->paginate($perPage);

        // Add due_date and surcharge to each record
        $records->getCollection()->transform(function ($record) {
            $record->due_date = $this->getDueDate($record->user, $record->reading_date);
            $record->surcharge = $this->calculateSurcharge($record, $record->due_date);
            return $record;
        });

        $serial_number = Auth::user()->serial_number;

        return Inertia::render('Admin/Records', [
            'serial_number' => $serial_number,
            'records' => $records,
            'filters' => $request->only(['search', 'status', 'month', 'year', 'perPage']),
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function show(MeterReading $record)
    {
        $record->load('user');
        $record->due_date = $this->getDueDate($record->user, $record->reading_date);
        $record->surcharge = $this->calculateSurcharge($record, $record->due_date);

        // Return JSON if it's an AJAX request (for modal)
        if (request()->ajax() || request()->expectsJson()) {
            return response()->json($record);
        }

        // Otherwise return the Inertia page
        return Inertia::render('Admin/Records/Show', [
            'record' => $record,
        ]);
    }

    public function edit(MeterReading $record)
    {
        $record->load('user');
        $record->due_date = $this->getDueDate($record->user, $record->reading_date);
        $record->surcharge = $this->calculateSurcharge($record, $record->due_date);
        return Inertia::render('Admin/Records/Edit', [
            'record' => $record,
        ]);
    }

    public function update(Request $request, MeterReading $record)
    {
        $validated = $request->validate([
            'reading' => 'sometimes|numeric',
            'consumption' => 'sometimes|numeric',
            'amount' => 'sometimes|numeric',
            'status' => 'sometimes|in:Paid,Pending,Overdue'
        ]);

        $record->update($validated);

        return redirect()->route('admin.records.index')
            ->with('success', 'Record updated successfully.');
    }

    public function destroy(MeterReading $record)
    {
        $record->delete();

        return redirect()->back()
            ->with('success', 'Record deleted successfully.');
    }

    public function details(MeterReading $record)
    {
        $record->load('user');
        $record->due_date = $this->getDueDate($record->user, $record->reading_date);
        $record->surcharge = $this->calculateSurcharge($record, $record->due_date);
        return response()->json($record);
    }
}
