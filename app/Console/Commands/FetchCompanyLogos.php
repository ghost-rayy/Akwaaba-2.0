<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FetchCompanyLogos extends Command
{
    protected $signature = 'companies:fetch-logos';
    protected $description = 'Fetch company logos from the web via Clearbit and fallbacks';

    private array $domainMap = [
        'Tech Ghana Ltd' => 'techghana.com',
        'MTN Ghana' => 'mtn.com.gh',
        'Telecel Ghana' => 'telecel.com.gh',
        'AT Ghana' => 'at.com.gh',
        'Ecobank Ghana' => 'ecobank.com',
        'GCB Bank' => 'gcbbank.com.gh',
        'Absa Bank Ghana' => 'absa.com.gh',
        'Stanbic Bank Ghana' => 'stanbicbank.com.gh',
        'Fidelity Bank Ghana' => 'fidelitybank.com.gh',
        'CalBank' => 'calbank.net',
        'Access Bank Ghana' => 'ghana.accessbankplc.com',
        'Zenith Bank Ghana' => 'zenithbank.com',
        'Republic Bank Ghana' => 'republicghana.com',
        'Universal Merchant Bank' => 'umbbank.com',
        'Societe Generale Ghana' => 'societegenerale.com.gh',
        'Consolidated Bank Ghana' => 'cbgh.com',
        'Unilever Ghana' => 'unilever.com',
        'Nestlé Ghana' => 'nestle.com.gh',
        'Guinness Ghana Breweries' => 'guinness.com',
        'Accra Brewery Limited' => 'accrabrewery.com.gh',
        'Fan Milk Limited' => 'fanmilk.com',
        'Coca-Cola Bottling Company of Ghana' => 'coca-cola.com',
        'PZ Cussons Ghana' => 'pzcussons.com',
        'Kasapreko Company Limited' => 'kasapreko.com',
        'Melcom Ghana' => 'melcomgroup.com',
        'GOIL PLC' => 'goil.com.gh',
        'TotalEnergies Marketing Ghana' => 'totalenergies.com',
        'Vivo Energy Ghana' => 'vivoenergy.com',
        'Ghana National Petroleum Corporation' => 'gnpcghana.com',
        'Tullow Ghana' => 'tullowoil.com',
        'Kosmos Energy Ghana' => 'kosmosenergy.com',
        'Volta River Authority' => 'vra.com',
        'GRIDCo' => 'gridcogh.com',
        'Electricity Company of Ghana' => 'ecg.com.gh',
        'Ghana Water Company Limited' => 'gwcl.com.gh',
        'Ghana Ports and Harbours Authority' => 'ghanaports.gov.gh',
        'Ghana Cocoa Board' => 'cocobod.gh',
        'SSNIT' => 'ssnit.org.gh',
        'Ghana Revenue Authority' => 'gra.gov.gh',
        'National Health Insurance Authority' => 'nhis.gov.gh',
        'First National Bank Ghana' => 'firstnationalbank.com.gh',
        'Bank of Africa Ghana' => 'boaghana.com',
        'Prudential Bank Ghana' => 'prudentialbank.com.gh',
        'Agricultural Development Bank' => 'agricbank.com',
        'Letshego Ghana' => 'letshego.com.gh',
        'Enterprise Group' => 'enterprisegroup.com.gh',
        'SIC Insurance Company' => 'sic-gh.com',
        'Hollard Insurance Ghana' => 'hollard.com.gh',
        'Star Assurance Company' => 'starassurance.com',
        'Tobinco Pharmaceuticals' => 'tobinco.com',
        'Niche Cocoa Industry' => 'nichecocoa.com',
    ];

    public function handle(): void
    {
        $companies = Company::whereNull('logo_path')->get();

        if ($companies->isEmpty()) {
            $this->info('All companies already have logos.');
            return;
        }

        $this->info("Processing {$companies->count()} companies...");

        $success = 0;
        $failed = 0;

        foreach ($companies as $company) {
            $domain = $this->domainMap[$company->name] ?? null;

            if (! $domain) {
                $this->warn("No domain mapped for: {$company->name}");
                $failed++;
                continue;
            }

            $this->line("  [{$company->id}] {$company->name} → {$domain}");

            $logoData = $this->fetchFromClearbit($domain);

            if (! $logoData) {
                $this->line('    Clearbit miss, trying Google favicons...');
                $logoData = $this->fetchFromGoogleFavicons($domain);
            }

            if (! $logoData) {
                $this->line('    Google favicons miss, trying direct favicon...');
                $logoData = $this->fetchDirectFavicon($domain);
            }

            if ($logoData) {
                $ext = $this->detectExtension($logoData, $domain);
                $path = 'logos/' . $company->id . '/logo.' . $ext;
                Storage::disk('public')->put($path, $logoData);
                $company->update(['logo_path' => $path]);
                $this->info("    ✓ Logo saved ({$ext}, " . strlen($logoData) . ' bytes)');
                $success++;
            } else {
                $this->warn("    ✗ No logo found");
                $failed++;
            }
        }

        $this->newLine();
        $this->table(
            ['Result', 'Count'],
            [
                ['Success', $success],
                ['Failed', $failed],
                ['Total', $success + $failed],
            ]
        );
    }

    private function fetchFromClearbit(string $domain): ?string
    {
        $url = "https://logo.clearbit.com/{$domain}";
        $data = $this->fetchUrl($url);

        if ($data && $this->isValidImage($data)) {
            return $data;
        }

        return null;
    }

    private function fetchFromGoogleFavicons(string $domain): ?string
    {
        $url = "https://www.google.com/s2/favicons?domain={$domain}&sz=128";
        $data = $this->fetchUrl($url);

        if ($data && $this->isValidImage($data) && strlen($data) > 200) {
            return $data;
        }

        return null;
    }

    private function fetchDirectFavicon(string $domain): ?string
    {
        $url = "https://{$domain}/favicon.ico";
        $data = $this->fetchUrl($url);

        if ($data && $this->isValidImage($data) && strlen($data) > 200) {
            return $data;
        }

        return null;
    }

    private function fetchUrl(string $url): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ]);

        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 400 && $data !== false && strlen($data) > 0) {
            return $data;
        }

        return null;
    }

    private function isValidImage(string $data): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $data);
        finfo_close($finfo);

        return in_array($mime, ['image/png', 'image/jpeg', 'image/gif', 'image/webp', 'image/x-icon', 'image/vnd.microsoft.icon'], true);
    }

    private function detectExtension(string $data, string $domain): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $data);
        finfo_close($finfo);

        return match ($mime) {
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/x-icon', 'image/vnd.microsoft.icon' => 'ico',
            default => 'png',
        };
    }
}
