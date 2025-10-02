<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    protected $zones = [
        'Zone 1' => ['Poblacion Sur'],
        'Zone 2' => ['Poblacion Centro'],
        'Zone 3' => ['Poblacion Centro'],
        'Zone 4' => ['Poblacion Norte'],
        'Zone 5' => ['Candajec', 'Buangan'],
        'Zone 6' => ['Bonbon'],
        'Zone 7' => ['Bonbon'],
        'Zone 8' => ['Nahawan'],
        'Zone 9' => ['Caboy', 'Villaflor', 'Cantuyoc'],
        'Zone 10' => ['Bacani', 'Mataub', 'Comaang', 'Tangaran'],
        'Zone 11' => ['Cantuyoc', 'Nahawan'],
        'Zone 12' => ['Lajog', 'Buacao'],
    ];

    protected $base_priorities = [
        'Burst pipe' => 'high',
        'No water supply' => 'high',
        'Cloudy or dirty water' => 'medium',
        'Smelly water' => 'medium',
        'Clogged pipes' => 'medium',
        'Rusty water' => 'low',
        'Low water pressure' => 'low',
        'Hot water issues' => 'low',
        'Running toilet' => 'low',
        'others' => 'medium',
    ];

    /**
     * Get comprehensive report statistics
     */
    public function getReportStats()
    {
        try {
            // Total Reports (excluding deleted)
            $totalReports = Report::whereNull('deleted_at')->count();

            // Resolved Reports (excluding deleted)
            $resolvedReports = Report::where('status', 'resolved')
                ->whereNull('deleted_at')
                ->count();

            // Pending Reports
            $pendingReports = Report::where('status', 'pending')
                ->whereNull('deleted_at')
                ->count();

            // In Progress Reports
            $inProgressReports = Report::where('status', 'in_progress')
                ->whereNull('deleted_at')
                ->count();

            // Calculate resolution percentage (avoid division by zero)
            $resolutionPercentage = $totalReports > 0
                ? round(($resolvedReports / $totalReports) * 100, 1)
                : 0;

            return [
                'total_reports' => $totalReports,
                'resolved_reports' => $resolvedReports,
                'pending_reports' => $pendingReports,
                'in_progress_reports' => $inProgressReports,
                'resolution_percentage' => $resolutionPercentage,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch report stats: ' . $e->getMessage());
            return [
                'total_reports' => 0,
                'resolved_reports' => 0,
                'pending_reports' => 0,
                'in_progress_reports' => 0,
                'resolution_percentage' => 0,
            ];
        }
    }

    /**
     * Get dashboard statistics combining users and reports
     */
    public function getDashboardStats()
    {
        try {
            // User statistics
            $activeUsers = User::where('enabled', true)->count();
            $totalUsers = User::count();
            $newUsersThisMonth = User::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            // Report statistics
            $reportStats = $this->getReportStats();

            return array_merge([
                'active_users' => $activeUsers,
                'total_users' => $totalUsers,
                'new_users_this_month' => $newUsersThisMonth,
            ], $reportStats);
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard stats: ' . $e->getMessage());
            return [
                'active_users' => 0,
                'total_users' => 0,
                'new_users_this_month' => 0,
                'total_reports' => 0,
                'resolved_reports' => 0,
                'pending_reports' => 0,
                'in_progress_reports' => 0,
                'resolution_percentage' => 0,
            ];
        }
    }


    /**
     * Improved method to find existing reports for merging
     */
    protected function findExistingReportForMerging($zone, $barangay, $issueType, $latitude, $longitude, $purok)
    {
        try {
            $radius = 5; // meters
            $earthRadius = 6371000; // meters

            if (!is_numeric($latitude) || !is_numeric($longitude)) {
                throw new \Exception('Invalid latitude or longitude values');
            }

            // First get all potential matches by basic criteria
            $potentialReports = Report::where('zone', $zone)
                ->where('barangay', $barangay)
                ->where('water_issue_type', $issueType)
                ->where('purok', $purok)
                ->whereNull('deleted_at')
                ->where('status', '!=', 'resolved') // Don't merge with resolved reports
                ->get();

            // Now filter by distance using more accurate Haversine formula
            $matchingReports = $potentialReports->filter(function ($report) use ($latitude, $longitude, $radius, $earthRadius) {
                if (is_null($report->latitude) || is_null($report->longitude)) {
                    return false;
                }

                // Haversine formula for accurate distance calculation
                $latFrom = deg2rad($latitude);
                $lonFrom = deg2rad($longitude);
                $latTo = deg2rad($report->latitude);
                $lonTo = deg2rad($report->longitude);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

                $distance = $angle * $earthRadius;

                return $distance <= $radius;
            });

            return $matchingReports->first();
        } catch (\Exception $e) {
            Log::error('Duplicate check failed', [
                'error' => $e->getMessage(),
                'zone' => $zone,
                'barangay' => $barangay,
                'issueType' => $issueType,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'purok' => $purok,
            ]);
            return null;
        }
    }

    public function store(Request $request)
    {
        try {
            Log::debug('Report submission started', [
                'input' => $request->all(),
                'files' => $request->file('photos') ? array_map(function ($file) {
                    return [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime' => $file->getClientMimeType(),
                    ];
                }, $request->file('photos')) : [],
            ]);

            $validated = $request->validate([
                'municipality' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'zone' => 'required|string',
                'barangay' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($request) {
                        $zone = $request->input('zone');
                        if (!isset($this->zones[$zone]) || !in_array($value, $this->zones[$zone])) {
                            $fail('The selected barangay is not valid for the chosen zone.');
                        }
                    },
                ],
                'purok' => 'required|string|max:255',
                'water_issue_type' => [
                    'required',
                    'string',
                    'max:255',
                    'in:Burst pipe,Rusty water,Low water pressure,No water supply,Clogged pipes,Smelly water,Cloudy or dirty water,Hot water issues,Running toilet,others',
                ],
                'custom_water_issue' => 'nullable|required_if:water_issue_type,others|string|max:100',
                'description' => 'required|string',
                'photos' => 'required|array|min:1|max:5',
                'photos.*' => [
                    'file',
                    'mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm',
                    function ($attribute, $value, $fail) {
                        $originalName = $value->getClientOriginalName();
                        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                        $imageExtensions = ['jpeg', 'png', 'jpg', 'gif', 'webp'];
                        $videoExtensions = ['mp4', 'mov', 'avi', 'webm'];
                        if (!in_array($extension, array_merge($imageExtensions, $videoExtensions))) {
                            $fail('The file must be an image (jpeg,png,jpg,gif,webp) or video (mp4,mov,avi,webm).');
                        }
                        if (in_array($extension, $imageExtensions) && $value->getSize() > 5 * 1024 * 1024) {
                            $fail('The photo must not exceed 5MB.');
                        }
                        if (in_array($extension, $videoExtensions) && $value->getSize() > 25 * 1024 * 1024) {
                            $fail('The video must not exceed 25MB.');
                        }
                    },
                ],
                'reporter_name' => 'required|string|max:255',
                'reporter_phone' => 'nullable|string|max:11',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $issueType = $validated['water_issue_type'];
            $zone = $validated['zone'];
            $barangay = $validated['barangay'];
            $purok = $validated['purok'];

            // Find existing report to merge with using improved method
            $existingReport = $this->findExistingReportForMerging($zone, $barangay, $issueType, $latitude, $longitude, $purok);

            $trackingCode = null;
            $reportId = null;
            $isMerged = false;
            $mainReport = null;

            if ($existingReport) {
                // Merge with existing report - use the main tracking code
                $trackingCode = $existingReport->tracking_code;
                $reportId = $existingReport->id;
                $isMerged = true;
                $mainReport = $existingReport;

                // Update reporter names (merge)
                $newReporters = trim($existingReport->reporter_name . ', ' . $validated['reporter_name']);
                $existingReport->reporter_name = $newReporters;

                // Update reporter phones if provided
                if ($validated['reporter_phone']) {
                    $existingPhones = $existingReport->reporter_phone ? explode(',', $existingReport->reporter_phone) : [];
                    $existingPhones[] = $validated['reporter_phone'];
                    $existingReport->reporter_phone = implode(', ', array_filter(array_unique($existingPhones)));
                }

                // Update additional tracking codes (unlimited merges)
                $additionalCodes = $existingReport->additional_tracking_codes ? json_decode($existingReport->additional_tracking_codes, true) : [];
                if (!is_array($additionalCodes)) $additionalCodes = [];

                $newTrackingCode = 'AQT' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
                $additionalCodes[] = $newTrackingCode;
                $existingReport->additional_tracking_codes = json_encode(array_unique($additionalCodes));

                // Append new description to existing one
                if ($validated['description'] !== $existingReport->description) {
                    $existingReport->description = $existingReport->description . "\n\n--- Additional Report ---\n" . $validated['description'];
                }

                $existingReport->save();

                Log::info('Report merged', [
                    'report_id' => $reportId,
                    'tracking_code' => $trackingCode,
                    'new_tracking_code' => $newTrackingCode,
                    'merged_reporters' => $newReporters,
                ]);
            } else {
                // Create new report
                $datePart = now()->format('Ymd');
                $randomPart = strtoupper(Str::random(4));
                $trackingCode = 'AQT' . $datePart . '-' . $randomPart;
                Log::debug('Generated new tracking code', ['tracking_code' => $trackingCode]);

                $reportData = [
                    'municipality' => $validated['municipality'],
                    'province' => $validated['province'],
                    'barangay' => $validated['barangay'],
                    'purok' => $validated['purok'],
                    'zone' => $validated['zone'],
                    'water_issue_type' => $validated['water_issue_type'],
                    'custom_water_issue' => $validated['custom_water_issue'] ?? null,
                    'description' => $validated['description'],
                    'reporter_name' => $validated['reporter_name'],
                    'reporter_phone' => $validated['reporter_phone'] ?? null,
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                    'tracking_code' => $trackingCode,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'priority' => $this->base_priorities[$issueType] ?? 'low', // Set initial priority
                ];

                $report = Report::create($reportData);
                $reportId = $report->id;
                $mainReport = $report;

                Log::info('New report created', [
                    'report_id' => $reportId,
                    'tracking_code' => $trackingCode,
                ]);
            }

            // Always add photos to the report ID (merged or new)
            foreach ($request->file('photos') as $photo) {
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $extension;
                $path = $photo->storeAs('report-photos', $filename, 'public');

                ReportPhoto::create([
                    'report_id' => $reportId,
                    'path' => $path,
                    'original_name' => $originalName,
                    'mime_type' => $photo->getClientMimeType(),
                    'size' => $photo->getSize(),
                ]);
            }

            // Determine and set priority based on number of reporters
            if ($mainReport) {
                $base_priority = $this->base_priorities[$issueType] ?? 'low';
                $reporters = explode(',', $mainReport->reporter_name);
                $num_reporters = count(array_filter(array_map('trim', $reporters)));
                $priority = $base_priority;

                if ($base_priority === 'low') {
                    if ($num_reporters >= 4) {
                        $priority = 'high';
                    } elseif ($num_reporters >= 2) {
                        $priority = 'medium';
                    }
                } elseif ($base_priority === 'medium' && $num_reporters >= 3) {
                    $priority = 'high';
                } // High priority issues remain 'high' regardless of reporter count

                if ($mainReport->priority != $priority) {
                    $mainReport->priority = $priority;
                    $mainReport->save();

                    Log::info('Priority updated', [
                        'report_id' => $mainReport->id,
                        'old_priority' => $base_priority,
                        'new_priority' => $priority,
                        'num_reporters' => $num_reporters,
                    ]);
                }
            }

            // Log activity for new reports only
            if (!$isMerged) {
                Activity::create([
                    'event' => 'report_created',
                    'description' => 'New report submitted: ' . $trackingCode,
                    'subject_type' => get_class($mainReport),
                    'subject_id' => $reportId,
                    'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
                    'causer_id' => Auth::id(),
                    'properties' => [
                        'tracking_code' => $trackingCode,
                        'zone' => $validated['zone'],
                        'status' => 'pending',
                    ],
                ]);
            } else {
                // Log merging activity
                Activity::create([
                    'event' => 'report_merged',
                    'description' => 'Report merged into existing report: ' . $trackingCode,
                    'subject_type' => get_class($mainReport),
                    'subject_id' => $reportId,
                    'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
                    'causer_id' => Auth::id(),
                    'properties' => [
                        'main_tracking_code' => $trackingCode,
                        'new_tracking_code' => $newTrackingCode ?? null,
                        'zone' => $validated['zone'],
                        'reporter_added' => $validated['reporter_name'],
                    ],
                ]);
            }

            if (Auth::check()) {
                return redirect()->route('customer.reports')
                    ->with('swal', [
                        'icon' => 'success',
                        'title' => $isMerged ? 'Report Merged' : 'Report Submitted',
                        'text' => $isMerged ? 'Your report has been merged with an existing one! Use this tracking code to monitor progress.' : 'Your report has been submitted successfully!',
                        'trackingCode' => $trackingCode,
                        'isMerged' => $isMerged,
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => $isMerged ? 'Report merged successfully. Use the main tracking code to monitor progress.' : 'Report submitted successfully',
                'trackingCode' => $trackingCode,
                'dateSubmitted' => now()->toISOString(),
                'reportId' => $reportId,
                'isMerged' => $isMerged,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed', ['errors' => $e->errors(), 'request' => $request->all()]);
            if (Auth::check()) {
                return redirect()->back()->withErrors($e->errors())->withInput();
            }
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the form for errors.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Report submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report. Please try again.',
            ], 500);
        }
    }

    public function destroy(Request $request, Report $report)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $report->status = "Deleted: " . $validated['reason'];
        $report->save();
        $report->delete();

        Activity::create([
            'event' => 'report_deleted',
            'description' => "Report deleted with reason: {$validated['reason']}",
            'subject_type' => get_class($report),
            'subject_id' => $report->id,
            'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
            'causer_id' => Auth::id(),
            'properties' => [
                'tracking_code' => $report->tracking_code,
                'reason' => $validated['reason'],
            ],
        ]);

        return redirect()->route('admin.reports')->with([
            'swal' => [
                'icon' => 'success',
                'title' => 'Report Deleted',
                'text' => 'Report deleted successfully.',
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Reports/Index', [
            'zones' => $this->zones,
        ]);
    }

    public function success(Request $request)
    {
        if (!$request->session()->has('trackingCode')) {
            return redirect()->route('home');
        }

        return Inertia::render('Reports/Success', [
            'trackingCode' => $request->session()->get('trackingCode'),
            'dateSubmitted' => $request->session()->get('dateSubmitted'),
        ]);
    }

    public function customerIndex(Request $request)
    {
        try {
            $reports = Report::with(['photos'])
                ->where('user_id', Auth::id())
                ->whereNull('deleted_at')
                ->latest()
                ->paginate(10);

            return Inertia::render('Customer/Reports', [
                'reports' => $reports,
                'zones' => $this->zones,
                'swal' => $request->session()->get('swal'),
            ]);
        } catch (\Exception $e) {
            Log::error('Customer reports fetch failed', ['error' => $e->getMessage()]);
            return Inertia::render('Customer/Reports', [
                'reports' => [],
                'zones' => $this->zones,
                'swal' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Failed to load reports.',
                ],
            ]);
        }
    }

    /**
     * Display the main welcome page with hero section
     */
    public function index()
    {
        $dashboardStats = $this->getDashboardStats();

        return Inertia::render('Welcome', [
            'canLogin' => true,
            'canRegister' => true,
            'dashboardStats' => [
                'active_users' => $dashboardStats['active_users'],
                'resolution_percentage' => $dashboardStats['resolution_percentage'],
                'resolved_reports' => $dashboardStats['resolved_reports'], // Add this
                'total_reports' => $dashboardStats['total_reports'], // Add this for context
            ],
        ]);
    }

    public function adminIndex(Request $request)
    {
        try {
            $query = Report::with(['photos', 'user'])
                ->whereNull('deleted_at')
                ->latest();

            if ($request->userType === 'guest') {
                $query->whereNull('user_id');
            } elseif ($request->userType === 'authenticated') {
                $query->whereNotNull('user_id');
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $search = $request->search;
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('tracking_code', 'like', "%{$search}%")
                        ->orWhere('reporter_name', 'like', "%{$search}%")
                        ->orWhere('water_issue_type', 'like', "%{$search}%")
                        ->orWhere('custom_water_issue', 'like', "%{$search}%");
                });
            }

            $reports = $query->paginate(5)
                ->appends($request->query());

            // Handle pre-selected report from query parameter
            $selectedReport = null;
            if ($request->filled('report_id')) {
                $selectedReport = Report::with(['photos', 'user'])->find($request->input('report_id'));
                if (!$selectedReport || $selectedReport->deleted_at) {
                    $selectedReport = null; // Reset if not found or deleted
                } else {
                    Log::info('Selected report found', ['report_id' => $request->input('report_id'), 'data' => $selectedReport->toArray()]);
                }
            }

            // Get report statistics - FIXED: Added this line
            $reportStats = $this->getReportStats();

            return Inertia::render('Admin/Reports', [
                'reports' => $reports,
                'filters' => $request->only(['userType', 'status', 'search']),
                'canEdit' => true,
                'canDelete' => true,
                'swal' => $request->session()->get('swal'),
                'selectedReport' => $selectedReport,
                'report_id' => $request->input('report_id'),
                'reportStats' => $reportStats, // FIXED: Now properly defined
            ]);
        } catch (\Exception $e) {
            Log::error('Admin reports fetch failed', ['error' => $e->getMessage()]);
            return Inertia::render('Admin/Reports', [
                'reports' => [],
                'filters' => $request->only(['userType', 'status', 'search']),
                'canEdit' => true,
                'canDelete' => true,
                'swal' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Failed to load reports.',
                ],
                'report_id' => $request->input('report_id'),
                'reportStats' => $this->getReportStats(), // FIXED: Added fallback
            ]);
        }
    }

    public function track(Request $request)
    {
        try {
            if ($request->isMethod('get')) {
                return Inertia::render('Reports/Track');
            }

            $request->validate([
                'tracking_code' => 'required|string',
            ]);

            $report = Report::with(['photos'])
                ->withTrashed() // Include soft-deleted reports
                ->where(function ($query) use ($request) {
                    $query->where('tracking_code', $request->tracking_code)
                        ->orWhereJsonContains('additional_tracking_codes', $request->tracking_code);
                })
                ->first();

            if (!$report) {
                return Inertia::render('Reports/Track', [
                    'swal' => [
                        'icon' => 'error',
                        'title' => 'Error',
                        'text' => 'Report not found with this tracking code.',
                    ],
                ]);
            }

            // If the report is deleted, pass the deletion reason to the view
            if ($report->trashed()) {
                return Inertia::render('Reports/Track', [
                    'report' => null,
                    'isDeleted' => true,
                    'deletionReason' => $report->status, // Pass the deletion reason
                ]);
            }

            return Inertia::render('Reports/Track', [
                'report' => $report,
            ]);
        } catch (\Exception $e) {
            Log::error('Report tracking failed', ['error' => $e->getMessage()]);
            return Inertia::render('Reports/Track', [
                'swal' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Report not found or an error occurred.',
                ],
            ]);
        }
    }

    public function findByTrackingCode(Request $request)
    {
        try {
            $request->validate([
                'tracking_code' => 'required|string',
            ]);

            $report = Report::with(['photos'])
                ->withTrashed() // Include soft-deleted reports
                ->where(function ($query) use ($request) {
                    $query->where('tracking_code', $request->tracking_code)
                        ->orWhereJsonContains('additional_tracking_codes', $request->tracking_code);
                })
                ->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'No report found with this tracking code.',
                ], 404);
            }

            // If the report is soft-deleted, return a specific response
            if ($report->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This report has been deleted.',
                    'deleted' => true,
                    'reason' => $report->status, // Return the deletion reason (e.g., "Deleted: [reason]")
                ], 410); // HTTP 410 Gone is appropriate for deleted resources
            }

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            Log::error('Find by tracking code failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve report.',
            ], 500);
        }
    }

    public function updateStatus(Request $request, Report $report)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,in_progress,resolved',
            ]);

            $oldStatus = $report->status;
            $newStatus = $request->status;

            // Synchronize status for all linked reports (unlimited merges)
            $trackingCode = $report->tracking_code;
            $linkedReports = Report::where(function ($query) use ($trackingCode) {
                $query->where('tracking_code', $trackingCode)
                    ->orWhereJsonContains('additional_tracking_codes', $trackingCode);
            })->get();

            foreach ($linkedReports as $linkedReport) {
                $linkedReport->status = $newStatus;
                $linkedReport->save();

                Activity::create([
                    'event' => 'report_status_changed',
                    'description' => "Report status changed from {$oldStatus} to {$newStatus}",
                    'subject_type' => get_class($linkedReport),
                    'subject_id' => $linkedReport->id,
                    'causer_type' => get_class(Auth::user()),
                    'causer_id' => Auth::id(),
                    'properties' => [
                        'tracking_code' => $linkedReport->tracking_code,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                    ],
                ]);
            }

            return redirect()->route('admin.reports')->with([
                'swal' => [
                    'icon' => 'success',
                    'title' => 'Status Updated',
                    'text' => 'Report status updated successfully for all linked reports!',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Status update failed', ['error' => $e->getMessage()]);
            return redirect()->route('admin.reports')->with([
                'swal' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Failed to update status: ' . $e->getMessage(),
                ],
            ]);
        }
    }

    /**
     * Get statistics API endpoint (for AJAX updates)
     */
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
