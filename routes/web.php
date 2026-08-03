<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DonasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $stats = [
        'total_reports' => \App\Models\Report::count(),
        'active_reports' => \App\Models\Report::where('disaster_status', 'Terjadi')->count(),
        'total_users' => \App\Models\User::count(),
        'total_donations' => \App\Models\Donation::sum('amount'),
    ];
    return view('welcome', compact('stats'));
})->name('welcome');

// Static Education Routes
Route::prefix('edukasi')->name('edukasi.')->group(function () {
    Route::get('/tas-siaga', function () { return view('edukasi.tas-siaga'); })->name('tas-siaga');
    Route::get('/gempa', function () { return view('edukasi.gempa'); })->name('gempa');
    Route::get('/peringatan-dini', function () { return view('edukasi.peringatan-dini'); })->name('peringatan-dini');
});

Route::get('/api/disaster-data', [ReportController::class, 'getDisasterData']);
Route::middleware('auth')->group(function () {
    Route::get('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout.get');
    Route::get('/donasi', [DonasiController::class, 'index'])->name('donasi');
    Route::post('/donasi', [DonasiController::class, 'store'])->name('donasi.store');

    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->middleware('admin')->name('admin.dashboard');

    Route::middleware('admin')->group(function () {
        Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports');
        Route::get('/admin/reports/create', [AdminReportController::class, 'create'])->name('admin.reports.create');
        Route::post('/admin/reports', [AdminReportController::class, 'store'])->name('admin.reports.store');
        Route::get('/admin/reports/{report}/edit', [AdminReportController::class, 'edit'])->name('admin.reports.edit');
        Route::put('/admin/reports/{report}', [AdminReportController::class, 'update'])->name('admin.reports.update');
        Route::delete('/admin/reports/{report}', [AdminReportController::class, 'destroy'])->name('admin.reports.destroy');
        Route::patch('/admin/reports/{report}/goal', [AdminReportController::class, 'updateGoal'])->name('admin.report.goal');
        Route::patch('/admin/reports/{report}/confirm', [AdminReportController::class, 'confirm'])->name('admin.reports.confirm');

        // User Management
        Route::get('/admin/users/create', [AdminController::class, 'userCreate'])->name('admin.users.create');
        Route::post('/admin/users', [AdminController::class, 'userStore'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [AdminController::class, 'userEdit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [AdminController::class, 'userUpdate'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'userDestroy'])->name('admin.users.destroy');


    });
});

Route::middleware('auth')->resource('reports', ReportController::class);

Route::middleware('auth')->group(function () {
    Route::get('reports/{report}/donate', [DonasiController::class, 'create'])->name('reports.donate');
    Route::post('reports/{report}/donate', [DonasiController::class, 'store'])->name('reports.donate.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
