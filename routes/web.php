<?php

use App\Livewire\Company\Attendance as CompanyAttendance;
use App\Livewire\Company\Dashboard as CompanyDashboard;
use App\Livewire\Company\Departments as CompanyDepartments;
use App\Livewire\Company\EndorseLetters as CompanyEndorseLetters;
use App\Livewire\Company\Evaluation as CompanyEvaluation;
use App\Livewire\Company\Letters as CompanyLetters;
use App\Livewire\Company\ManagePersonnel as CompanyManagePersonnel;
use App\Livewire\Company\OnboardPersonnel as CompanyOnboard;
use App\Livewire\Company\Settings as CompanySettings;
use App\Livewire\Company\Shortlist as CompanyShortlist;
use App\Livewire\Personnel\Dashboard as PersonnelDashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {

    // Super Admin routes
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });

    // Company routes (company_admin + hr_staff)
    Route::middleware('role:company_admin,hr_staff')->prefix('company')->name('company.')->group(function () {
        Route::get('/dashboard', CompanyDashboard::class)->name('dashboard');
        Route::get('/onboard', CompanyOnboard::class)->name('onboard');
        Route::get('/departments', CompanyDepartments::class)->name('departments');
        Route::get('/shortlist', CompanyShortlist::class)->name('shortlist');
        Route::get('/manage-personnel', CompanyManagePersonnel::class)->name('personnel');
        Route::get('/attendance', CompanyAttendance::class)->name('attendance');
        Route::get('/evaluations', CompanyEvaluation::class)->name('evaluations');
        Route::get('/letters', CompanyLetters::class)->name('letters');
        // Admin-only routes
        Route::middleware('role:company_admin')->group(function () {
            Route::get('/endorse', CompanyEndorseLetters::class)->name('endorse');
            Route::get('/settings', CompanySettings::class)->name('settings');
        });
    });

    // NSS Personnel routes
    Route::middleware('role:nss_personnel')->prefix('personnel')->name('personnel.')->group(function () {
        Route::get('/dashboard', PersonnelDashboard::class)->name('dashboard');
    });

    // Fallback dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');
});

require __DIR__.'/auth.php';
