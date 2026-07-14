<?php

namespace App\Support;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Department;
use App\Models\EndorsedLetter;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\LetterTemplate;
use App\Models\User;

class CompanyNavCounters
{
    public static function for(User $user): array
    {
        $companyId = $user->company_id;

        if (! $companyId) {
            return self::empty();
        }

        $isAdmin = $user->isCompanyAdmin();

        $statusCounts = Enrollment::query()
            ->where('company_id', $companyId)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingReview = (int) ($statusCounts['pending_review'] ?? 0);
        $shortlisted = (int) ($statusCounts['shortlisted'] ?? 0);

        $pendingValidations = EndorsedLetter::query()
            ->whereHas('enrollment', fn ($q) => $q->where('company_id', $companyId))
            ->whereNotNull('validated_file_path')
            ->whereNull('validated_by')
            ->count();

        $activeUserIds = Enrollment::query()
            ->where('company_id', $companyId)
            ->whereIn('status', ['validated', 'active'])
            ->pluck('user_id');

        $pendingValidations = Attendance::query()
            ->where('company_id', $companyId)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('check_in')->whereNull('check_in_validated_at');
                })->orWhere(function ($q) {
                    $q->whereNotNull('check_out')->whereNull('check_out_validated_at');
                })->orWhere(function ($q) {
                    $q->where('status', 'absent')->whereNull('absence_validated_at');
                });
            })
            ->count();

        $dueEvaluations = 0;
        if ($activeUserIds->isNotEmpty()) {
            $recentlyEvaluated = Evaluation::query()
                ->where('company_id', $companyId)
                ->whereIn('user_id', $activeUserIds)
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct()
                ->count('user_id');

            $dueEvaluations = max(0, $activeUserIds->count() - $recentlyEvaluated);
        }

        $hasPostingTemplate = LetterTemplate::query()
            ->where('company_id', $companyId)
            ->where('type', 'posting_letter')
            ->where('is_active', true)
            ->exists();

        $departmentsWithoutHead = Department::query()
            ->where('company_id', $companyId)
            ->whereNull('head_id')
            ->count();

        $settingsTodo = 0;
        if ($isAdmin) {
            $company = Company::find($companyId);
            if ($company) {
                if (! $company->digital_signature_path) {
                    $settingsTodo++;
                }
                if (! $company->stamp_path) {
                    $settingsTodo++;
                }
                if (! $hasPostingTemplate && ! $company->posting_letter_path) {
                    $settingsTodo++;
                }
            }
        }

        return [
            'company.dashboard' => 0,
            'company.onboard' => 0,
            'company.shortlist' => $pendingReview,
            'company.endorse' => $isAdmin ? ($shortlisted + $pendingValidations) : 0,
            'company.personnel' => 0,
            'company.attendance' => $pendingValidations,
            'company.evaluations' => $dueEvaluations,
            'company.reports' => 0,
            'company.letters' => 0,
            'company.dms' => 0,
            'company.departments' => $departmentsWithoutHead,
            'company.settings' => $settingsTodo,
        ];
    }

    public static function empty(): array
    {
        return array_fill_keys([
            'company.dashboard',
            'company.onboard',
            'company.shortlist',
            'company.endorse',
            'company.personnel',
            'company.attendance',
            'company.evaluations',
            'company.reports',
            'company.letters',
            'company.dms',
            'company.departments',
            'company.settings',
        ], 0);
    }
}
