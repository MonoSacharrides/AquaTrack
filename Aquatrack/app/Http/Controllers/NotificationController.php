<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Announcements;
use App\Models\DismissedNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->role) {
            Log::error('User missing or invalid role', ['user_id' => Auth::id()]);
            return response()->json(['success' => false, 'message' => 'Invalid user role'], 400);
        }

        Log::info('Fetching notifications for user', ['user_id' => $user->id, 'role' => $user->role]);

        $notifications = collect();
        try {
            switch ($user->role) {
                case 'admin':
                    Log::info('Calling getAdminNotifications');
                    $notifications = $this->getAdminNotifications();
                    break;
                case 'staff':
                    Log::info('Calling getStaffNotifications');
                    $notifications = $this->getStaffNotifications();
                    break;
                default:
                    Log::info('Calling getCustomerNotifications');
                    $notifications = $this->getCustomerNotifications();
            }
        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }

        Log::info('Notifications fetched', ['count' => $notifications->count()]);

        $notifications = $notifications->sortByDesc(function ($item) {
            return $item['created_at'] ?? $item['updated_at'] ?? now();
        })->take(50)->values();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $notifications->where('unread', true)->count()
        ]);
    }

    /**
     * Get notifications for admin users
     */
    private function getAdminNotifications()
    {
        $notifications = collect();

        // New reports from customers/guests (last 7 days)
        $newReports = Report::with('user')
            ->where('created_at', '>=', now()->subDays(7))
            ->where('status', 'pending')
            ->latest()
            ->get();

        foreach ($newReports as $report) {
            $notifications->push([
                'id' => 'new_report_' . $report->id,
                'type' => 'new_report',
                'user_name' => $report->user ? $report->user->name : ($report->reporter_name ?? 'Guest User'),
                'user_avatar' => $report->user ? $report->user->avatar_url : null,
                'tracking_code' => $report->tracking_code ?? 'N/A',
                'barangay' => $report->barangay ?? 'Unknown',
                'municipality' => $report->municipality ?? 'Unknown',
                'status' => $report->status ?? 'pending',
                'report_id' => $report->id,
                'unread' => !($report->viewed_by_admin ?? false),
                'created_at' => $report->created_at ? $report->created_at->toISOString() : now()->toISOString(),
                'message' => "New water quality report submitted"
            ]);
        }

        // Recent report updates (last 3 days)
        $updatedReports = Report::with('user')
            ->where('updated_at', '>=', now()->subDays(3))
            ->where('updated_at', '!=', DB::raw('created_at'))
            ->latest('updated_at')
            ->get();

        foreach ($updatedReports as $report) {
            $notifications->push([
                'id' => 'report_update_' . $report->id,
                'type' => 'report_update',
                'user_name' => $report->user ? $report->user->name : ($report->reporter_name ?? 'System'),
                'user_avatar' => $report->user ? $report->user->avatar_url : null,
                'tracking_code' => $report->tracking_code ?? 'N/A',
                'barangay' => $report->barangay ?? 'Unknown',
                'municipality' => $report->municipality ?? 'Unknown',
                'status' => $report->status ?? 'unknown',
                'report_id' => $report->id,
                'unread' => !($report->update_viewed_by_admin ?? false),
                'updated_at' => $report->updated_at ? $report->updated_at->toISOString() : now()->toISOString(),
                'message' => "Report status updated"
            ]);
        }

        // System announcements
        $announcements = Announcements::where('active', true)
            ->where(function ($query) {
                $query->where('target_audience', 'admin')
                    ->orWhere('target_audience', 'all');
            })
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->get();

        foreach ($announcements as $announcement) {
            $notifications->push([
                'id' => 'announcement_' . $announcement->id,
                'type' => 'announcement',
                'title' => $announcement->title ?? 'Untitled',
                'message' => $announcement->content ?? 'No content', // Use content instead of message
                'unread' => !$this->isAnnouncementRead($announcement->id),
                'created_at' => $announcement->created_at ? $announcement->created_at->toISOString() : now()->toISOString()
            ]);
        }

        $notifications = $notifications->filter(function ($notification) {
            return !$this->isNotificationDismissed($notification['id']);
        });

        return $notifications;
    }

    /**
     * Get notifications for staff users
     */
    private function getStaffNotifications()
    {
        $notifications = collect();
        $user = Auth::user();

        // Assigned reports
        $assignedReports = Report::with('user') // Remove 'location'
            ->where('assigned_to', $user->id)
            ->where('updated_at', '>=', now()->subDays(7))
            ->latest('updated_at')
            ->get();

        foreach ($assignedReports as $report) {
            $notifications->push([
                'id' => 'assigned_report_' . $report->id,
                'type' => 'report_update',
                'user_name' => $report->user ? $report->user->name : ($report->reporter_name ?? 'System'),
                'user_avatar' => $report->user ? $report->user->avatar_url : null,
                'tracking_code' => $report->tracking_code ?? 'N/A',
                'barangay' => $report->barangay ?? 'Unknown',
                'municipality' => $report->municipality ?? 'Unknown',
                'status' => $report->status ?? 'unknown',
                'report_id' => $report->id,
                'unread' => !($report->viewed_by_staff ?? false),
                'updated_at' => $report->updated_at ? $report->updated_at->toISOString() : now()->toISOString(),
                'message' => "Report assigned to you"
            ]);
        }

        // Staff announcements
        $announcements = Announcements::where('active', true)
            ->where(function ($query) {
                $query->where('target_audience', 'staff')
                    ->orWhere('target_audience', 'all');
            })
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->get();

        foreach ($announcements as $announcement) {
            $notifications->push([
                'id' => 'announcement_' . $announcement->id,
                'type' => 'announcement',
                'title' => $announcement->title ?? 'Untitled',
                'message' => $announcement->message ?? 'No message',
                'unread' => !$this->isAnnouncementRead($announcement->id),
                'created_at' => $announcement->created_at ? $announcement->created_at->toISOString() : now()->toISOString()
            ]);
        }

        return $notifications;
    }

    /**
     * Get notifications for customer users
     */
    private function getCustomerNotifications()
    {
        $notifications = collect();
        $user = Auth::user();

        // User's report updates
        $userReports = Report::where('user_id', $user->id)
            ->where('updated_at', '>=', now()->subDays(14))
            ->latest('updated_at')
            ->get();

        foreach ($userReports as $report) {
            $notifications->push([
                'id' => 'my_report_' . $report->id,
                'type' => 'report_update',
                'tracking_code' => $report->tracking_code,
                'barangay' => $report->barangay,
                'municipality' => $report->municipality,
                'status' => $report->status,
                'report_id' => $report->id,
                'unread' => !$report->viewed_by_user,
                'updated_at' => $report->updated_at->toISOString(),
                'message' => "Your report status has been updated"
            ]);
        }

        // Customer announcements
        $announcements = Announcements::where('active', true)
            ->where('target_audience', 'customer')
            ->orWhere('target_audience', 'all')
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->get();

        foreach ($announcements as $announcement) {
            $notifications->push([
                'id' => 'announcement_' . $announcement->id,
                'type' => 'announcement',
                'title' => $announcement->title,
                'message' => $announcement->message,
                'unread' => !$this->isAnnouncementRead($announcement->id),
                'created_at' => $announcement->created_at->toISOString()
            ]);
        }

        return $notifications;
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request)
    {
        $notificationId = $request->input('notification_id');
        $user = Auth::user();

        // Parse notification ID to determine type and actual ID
        if (str_starts_with($notificationId, 'new_report_') || str_starts_with($notificationId, 'report_update_')) {
            $reportId = (int) str_replace(['new_report_', 'report_update_', 'assigned_report_', 'my_report_'], '', $notificationId);
            $report = Report::find($reportId);

            if ($report) {
                switch ($user->role) {
                    case 'admin':
                        $report->viewed_by_admin = true;
                        $report->update_viewed_by_admin = true;
                        break;
                    case 'staff':
                        $report->viewed_by_staff = true;
                        break;
                    default:
                        $report->viewed_by_user = true;
                }
                $report->save();
            }
        } elseif (str_starts_with($notificationId, 'announcement_')) {
            $announcementId = (int) str_replace('announcement_', '', $notificationId);
            $this->markAnnouncementAsRead($announcementId);
        }

        // Return redirect instead of JSON
        return redirect()->back()->with([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                Report::where('viewed_by_admin', false)
                    ->orWhere('update_viewed_by_admin', false)
                    ->update([
                        'viewed_by_admin' => true,
                        'update_viewed_by_admin' => true
                    ]);
                break;
            case 'staff':
                Report::where('assigned_to', $user->id)
                    ->where('viewed_by_staff', false)
                    ->update(['viewed_by_staff' => true]);
                break;
            default:
                Report::where('user_id', $user->id)
                    ->where('viewed_by_user', false)
                    ->update(['viewed_by_user' => true]);
        }

        // Mark all announcements as read
        $this->markAllAnnouncementsAsRead();

        // Return redirect instead of JSON
        return redirect()->back()->with([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $notifications = $this->index(request())->getData();
        $unreadCount = collect($notifications->notifications)->where('unread', true)->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Check if announcement is read by current user
     */
    private function isAnnouncementRead($announcementId)
    {
        return DB::table('announcement_reads')
            ->where('announcement_id', $announcementId)
            ->where('user_id', Auth::id())
            ->exists();
    }

    /**
     * Mark announcement as read
     */
    private function markAnnouncementAsRead($announcementId)
    {
        DB::table('announcement_reads')->updateOrInsert([
            'announcement_id' => $announcementId,
            'user_id' => Auth::id(),
        ], [
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Mark all announcements as read
     */
    private function markAllAnnouncementsAsRead()
    {
        $announcements = Announcements::where('active', true)->get();

        foreach ($announcements as $announcement) {
            $this->markAnnouncementAsRead($announcement->id);
        }
    }

    /**
     * Delete a notification (NEW METHOD)
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Log::info('Dismissing notification', ['notification_id' => $id, 'user_id' => $user->id]);

            // Store the dismissed notification
            DismissedNotification::create([
                'user_id' => $user->id,
                'notification_id' => $id,
                'type' => $this->getNotificationType($id)
            ]);

            return response()->json(['success' => true, 'message' => 'Notification dismissed successfully']);
        } catch (\Exception $e) {
            Log::error('Notification dismissal failed: ' . $e->getMessage(), [
                'notification_id' => $id,
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'Failed to dismiss notification'], 500);
        }
    }

    private function getNotificationType($notificationId)
    {
        if (str_starts_with($notificationId, 'new_report_')) {
            return 'new_report';
        } elseif (
            str_starts_with($notificationId, 'report_update_') ||
            str_starts_with($notificationId, 'assigned_report_') ||
            str_starts_with($notificationId, 'my_report_')
        ) {
            return 'report_update';
        } elseif (str_starts_with($notificationId, 'announcement_')) {
            return 'announcement';
        }

        return 'unknown';
    }

    private function isNotificationDismissed($notificationId)
    {
        return DismissedNotification::where('user_id', Auth::id())
            ->where('notification_id', $notificationId)
            ->exists();
    }
}
