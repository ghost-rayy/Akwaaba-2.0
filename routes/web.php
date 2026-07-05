<?php

use App\Http\Controllers\Company\DocumentUploadController as CompanyDocumentUploadController;
use App\Http\Controllers\Personnel\DocumentUploadController;
use App\Livewire\Admin\Companies as AdminCompanies;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Personnel as AdminPersonnel;
use App\Livewire\Company\Attendance as CompanyAttendance;
use App\Livewire\Company\Dashboard as CompanyDashboard;
use App\Livewire\Company\Departments as CompanyDepartments;
use App\Livewire\Company\EndorsedLetters as CompanyEndorsedLetters;
use App\Livewire\Company\EndorseLetters as CompanyEndorseLetters;
use App\Livewire\Company\Evaluation as CompanyEvaluation;
use App\Livewire\Company\Letters as CompanyLetters;
use App\Livewire\Company\ManagePersonnel as CompanyManagePersonnel;
use App\Livewire\Company\OnboardPersonnel as CompanyOnboard;
use App\Livewire\Company\Report as CompanyReport;
use App\Livewire\Company\Settings as CompanySettings;
use App\Livewire\Company\Shortlist as CompanyShortlist;
use App\Livewire\Personnel\Attendance as PersonnelAttendance;
use App\Livewire\Personnel\Dashboard as PersonnelDashboard;
use App\Livewire\Personnel\Documents as PersonnelDocuments;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/companies', AdminCompanies::class)->name('companies');
        Route::get('/personnel', AdminPersonnel::class)->name('personnel');
        Route::view('profile', 'profile')->name('profile');
    });
});

Route::middleware('auth:company')->prefix('company')->name('company.')->group(function () {
    Route::middleware('role:company_admin,hr_staff')->group(function () {
        Route::get('/dashboard', CompanyDashboard::class)->name('dashboard');
        Route::get('/onboard', CompanyOnboard::class)->name('onboard');
        Route::get('/departments', CompanyDepartments::class)->name('departments');
        Route::get('/shortlist', CompanyShortlist::class)->name('shortlist');
        Route::get('/manage-personnel', CompanyManagePersonnel::class)->name('personnel');
        Route::get('/attendance', CompanyAttendance::class)->name('attendance');
        Route::get('/evaluations', CompanyEvaluation::class)->name('evaluations');
        Route::get('/reports', CompanyReport::class)->name('reports');
        Route::get('/letters', CompanyLetters::class)->name('letters');
        Route::view('profile', 'profile')->name('profile');

        Route::middleware('role:company_admin')->group(function () {
            Route::get('/endorse', CompanyEndorseLetters::class)->name('endorse');
            Route::get('/endorsed-letters', CompanyEndorsedLetters::class)->name('endorsed-letters');
            Route::get('/settings', CompanySettings::class)->name('settings');
            Route::post('/upload/logo', [CompanyDocumentUploadController::class, 'logo'])->name('upload.logo');
            Route::post('/upload/stamp', [CompanyDocumentUploadController::class, 'stamp'])->name('upload.stamp');
            Route::post('/upload/signature', [CompanyDocumentUploadController::class, 'signature'])->name('upload.signature');
            Route::post('/upload/posting-letter', [CompanyDocumentUploadController::class, 'postingLetter'])->name('upload.posting-letter');
        });
    });
});

Route::middleware('auth:personnel')->prefix('personnel')->name('personnel.')->group(function () {
    Route::middleware('role:nss_personnel')->group(function () {
        Route::get('/dashboard', PersonnelDashboard::class)->name('dashboard');
        Route::view('profile', 'profile')->name('profile');
        Route::post('/upload/posting-letter', [DocumentUploadController::class, 'postingLetter'])->name('upload.posting-letter');
        Route::post('/upload/passport-photo', [DocumentUploadController::class, 'passportPhoto'])->name('upload.passport-photo');

        Route::middleware('completed.profile')->group(function () {
            Route::get('/attendance', PersonnelAttendance::class)->name('attendance');
            Route::get('/documents', PersonnelDocuments::class)->name('documents');
        });
    });
});

Route::middleware('auth.any')->group(function () {
    Route::get('/document/stream/{id}', function ($id) {
        $document = \App\Models\Document::findOrFail($id);
        $user = auth()->user();

        if ($user->role !== 'super_admin' && $user->company_id !== $document->company_id && $user->id !== $document->user_id) {
            abort(403);
        }

        $path = storage_path('app/public/'.$document->file_path);
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ]);
    })->name('document.stream');
});

require __DIR__.'/auth.php';
