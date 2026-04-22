<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HistoryController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Email verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerifyEmailController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware('throttle:6,1')->name('verification.send');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Trainings
    Route::resource('trainings', TrainingController::class);

    // Registrations
    Route::post('/trainings/{training}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations'])->name('registrations.my');

    // Admin/Faculty routes
    Route::middleware('role:admin,faculty')->group(function () {
        Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');
        Route::patch('/registrations/{registration}/status', [RegistrationController::class, 'updateStatus'])->name('registrations.updateStatus');

        // Attendance management
        Route::get('/trainings/{training}/attendance/generate', [AttendanceController::class, 'generate'])->name('attendance.generate');
        Route::post('/trainings/{training}/attendance/generate-qr', [AttendanceController::class, 'generateQR'])->name('attendance.generateQR');
        Route::get('/trainings/{training}/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');

        // Reports
        Route::get('/trainings/{training}/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/trainings/{training}/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{report}/pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
        Route::get('/reports/{report}/csv', [ReportController::class, 'exportCsv'])->name('reports.exportCsv');

        // Certificates management
        Route::get('/trainings/{training}/certificates', [CertificateController::class, 'manage'])->name('certificates.manage');
        Route::post('/trainings/{training}/certificates/activate', [CertificateController::class, 'activate'])->name('certificates.activate');
    });

    // Attendance check-in (for students)
    Route::get('/attendance/scan', [AttendanceController::class, 'showScanner'])->name('attendance.scan');
    Route::get('/attendance/checkin', [AttendanceController::class, 'checkinForm'])->name('attendance.checkin.form');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');

    // Certificates (student download)
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
});
