<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters from request
        $eventType = $request->get('event_type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        // Build query with filters
        $query = Activity::with('causer')->latest();

        if ($eventType && $eventType !== 'all') {
            $query->where('event', $eventType);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $activities = $query->paginate(5)->withQueryString();

        return Inertia::render('Admin/ActivityLogs', [
            'activities' => $activities->through(function ($activity) {
                return [
                    'id' => $activity->id,
                    'event' => $activity->event,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at,
                    'causer_name' => $activity->causer?->name ?? 'System',
                    'properties' => $activity->properties,
                    'subject_type' => $activity->subject_type,
                ];
            }),
            'filters' => [
                'event_type' => $eventType,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $search,
            ],
            'event_types' => [
                'all' => 'All Events',
                'created' => 'Created',
                'updated' => 'Updated',
                'deleted' => 'Deleted',
                'logged_in' => 'User Login',
                'logged_out' => 'User Logout',
                'report_created' => 'Report Created',
                'report_status_changed' => 'Report Status Changed',
            ],
        ]);
    }

    public function export(Request $request)
    {

        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
