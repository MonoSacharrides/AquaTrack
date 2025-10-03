<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRecordController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Customer\CustomerAnnouncementsController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerUsageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Roles\SelectRolesController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\StaffReadingController;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing/LandingPage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard redirection
Route::get('/redirect-to-dashboard', [AuthenticatedSessionController::class, 'redirectToDashboard'])
    ->middleware(['auth', 'verified'])
    ->name('redirect-to-dashboard');

Route::post('/verify-code', [AuthenticatedSessionController::class, 'verifyCode'])->name('verify-code');

// Add this route for generic dashboard access
Route::get('/dashboard', function () {
    // Redirect based on user role
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('staff')) {
        return redirect()->route('staff.dashboard');
    } elseif ($user->hasRole('customer')) {
        return redirect()->route('customer.dashboard');
    }

    return redirect()->route('home');
})->name('dashboard')->middleware(['auth', 'verified']);

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // ADMIN REPORTS ROUTES - Keep these inside the admin middleware
    Route::get('/admin/reports', [ReportController::class, 'adminIndex'])->name('admin.reports');
    Route::delete('/admin/reports/{report}', [ReportController::class, 'destroy'])->name('admin.reports.destroy');
    Route::post('/admin/reports/{report}/update-status', [ReportController::class, 'updateStatus'])->name('admin.reports.updateStatus');

    Route::get('/admin/records/{record}/details', [AdminRecordController::class, 'details'])->name('admin.records.details');
    Route::get('/admin/users', [AdminUsersController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [AdminUsersController::class, 'store'])->name('admin.users.store');
    Route::delete('/admin/users/{user}', [AdminUsersController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/admin/users/toggle-status', [AdminUsersController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::put('/admin/users/{user}', [AdminUsersController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{user}/reset-password', [AdminUsersController::class, 'resetPassword'])->name('admin.users.reset-password');

    // Stats routes
    Route::get('/admin/stats', [AdminUsersController::class, 'getStats'])->name('admin.stats');
    Route::get('/reports/stats', [ReportController::class, 'getStats'])->name('reports.stats');

    Route::post('/admin/export-water-analytics', [AdminDashboardController::class, 'exportWaterAnalytics'])->name('admin.export-water-analytics');

    Route::get('/admin/records', [AdminRecordController::class, 'index'])->name('admin.records.index');
    Route::get('/admin/records/{record}', [AdminRecordController::class, 'show'])->name('admin.records.show');
    Route::get('/admin/records/{record}/edit', [AdminRecordController::class, 'edit'])->name('admin.records.edit');
    Route::put('/admin/records/{record}', [AdminRecordController::class, 'update'])->name('admin.records.update');
    Route::delete('/admin/records/{record}', [AdminRecordController::class, 'destroy'])->name('admin.records.destroy');

    Route::get('/admin/announcements', [AnnouncementsController::class, 'index'])->name('announcements');
    Route::post('/admin/announcements', [AnnouncementsController::class, 'store'])->name('announcements.store');
    Route::put('/admin/announcements/{announcement}', [AnnouncementsController::class, 'update'])->name('announcements.update');
    Route::delete('/admin/announcements/{announcement}', [AnnouncementsController::class, 'destroy'])->name('announcements.destroy');

    Route::get('/admin/activity-logs', [AdminActivityLogController::class, 'index'])->name('admin.activity-logs');
    Route::get('/activity-logs/export', [AdminActivityLogController::class, 'export'])->name('admin.activity-logs.export');

    Route::get('/admin/notifications', function () {
        return Inertia::render('Admin/Notifications', [
            'notifications' => app(NotificationController::class)->index(request())->getData(true)['notifications'] ?? [],
        ]);
    })->name('admin.notifications');

    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Staff Routes
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/dashboard/data', [StaffDashboardController::class, 'getDashboardData'])->name('staff.dashboard.data');

    // Meter reading routes
    Route::get('/staff/reading', [StaffReadingController::class, 'index'])->name('staff.reading');
    Route::get('/staff/reading/search', [StaffReadingController::class, 'search'])->name('staff.reading.search');
    Route::get('/staff/reading/user/{userId}', [StaffReadingController::class, 'getUserDetails'])->name('staff.reading.user');
    Route::get('/staff/reading/previous/{userId}', [StaffReadingController::class, 'getPreviousReadings'])->name('staff.reading.previous');
    Route::post('/staff/reading', [StaffReadingController::class, 'storeReading'])->name('staff.reading.store');
    Route::put('/staff/reading/{readingId}/update', [StaffReadingController::class, 'updateReading'])->name('staff.reading.update');

    Route::get('/staff/notifications', function () {
        return Inertia::render('Staff/Notifications', [
            'notifications' => app(NotificationController::class)->index(request())->getData(true)['notifications'] ?? [],
        ]);
    })->name('staff.notifications');
});

// routes/web.php or routes/api.php
Route::get('/staff/readings/{userId}/previous', [StaffReadingController::class, 'getPreviousReadings']);

// Customer Routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/customer/usage', [CustomerUsageController::class, 'index'])->name('customer.usage');
    Route::get('/customer/reports', [ReportController::class, 'customerIndex'])
        ->middleware(['auth', 'verified'])
        ->name('customer.reports');
    Route::get('/customer/announcements', [CustomerAnnouncementsController::class, 'index'])->name('customer.announcements');

    Route::get('/customer/notifications', function () {
        return Inertia::render('Customer/Notifications', [
            'notifications' => app(NotificationController::class)->index(request())->getData(true)['notifications'] ?? [],
        ]);
    })->name('customer.notifications');
});

Route::post('/reports/sync-offline', [ReportController::class, 'syncOfflineReports'])->name('reports.sync-offline');

// Select Roles
Route::get('/select-roles', [SelectRolesController::class, 'index'])->name('select-roles');

// Notification Routes
Route::middleware('auth')->group(function () {
    Route::get('/api/notifications', [NotificationController::class, 'index']);
    Route::post('/api/notifications/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/api/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    // Add the delete notification route
    Route::delete('/api/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Report Routes (Public and Authenticated)
Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
Route::match(['get', 'post'], '/reports/track', [ReportController::class, 'track'])->name('reports.track');
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
Route::get('/api/reports/find', [ReportController::class, 'findByTrackingCode'])->name('reports.find');
Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');

Route::get('/debug/customers', function () {
    return User::where('role', 'customer')->get();
});

require __DIR__ . '/auth.php';
