<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GhanaPersonnelDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(0);

        $sourcePdf = public_path('2.pdf');
        if (! is_file($sourcePdf)) {
            $this->command?->error('Missing public/2.pdf');

            return;
        }

        Storage::disk('public')->makeDirectory('documents/seeded');
        Storage::disk('public')->makeDirectory('documents/seeded/passports');

        $letterPath = 'documents/seeded/posting_letter.pdf';
        if (! Storage::disk('public')->exists($letterPath)) {
            Storage::disk('public')->put($letterPath, file_get_contents($sourcePdf));
        }

        $letterFull = Storage::disk('public')->path($letterPath);
        $letterSize = filesize($letterFull) ?: null;

        $portraitPaths = $this->ensurePortraitPool();
        if ($portraitPaths === []) {
            $this->command?->error('Could not download passport portrait images.');

            return;
        }

        $usersQuery = User::query()
            ->where('role', 'nss_personnel')
            ->where('email', 'like', 'personnel%@gmail.com')
            ->whereNotNull('company_id')
            ->orderBy('id');

        $totalUsers = (clone $usersQuery)->count();
        $this->command?->info("Assigning documents to {$totalUsers} personnel...");

        $now = now()->format('Y-m-d H:i:s');
        $portraitCount = count($portraitPaths);
        $done = 0;
        $offset = 0;

        $usersQuery->chunkById(400, function ($users) use (
            &$done,
            &$offset,
            $letterPath,
            $letterSize,
            $portraitPaths,
            $portraitCount,
            $now
        ) {
            $userIds = $users->pluck('id')->all();

            $existing = Document::query()
                ->whereIn('user_id', $userIds)
                ->whereIn('type', ['posting_letter', 'passport'])
                ->get(['user_id', 'type'])
                ->groupBy('user_id')
                ->map(fn ($rows) => $rows->pluck('type')->all());

            $batch = [];

            foreach ($users as $user) {
                $types = $existing[$user->id] ?? [];

                if (! in_array('posting_letter', $types, true)) {
                    $batch[] = [
                        'user_id' => $user->id,
                        'company_id' => $user->company_id,
                        'type' => 'posting_letter',
                        'file_path' => $letterPath,
                        'original_name' => 'posting_letter.pdf',
                        'mime_type' => 'application/pdf',
                        'size' => $letterSize,
                        'is_verified' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (! in_array('passport', $types, true)) {
                    $photoPath = $portraitPaths[$offset % $portraitCount];
                    $batch[] = [
                        'user_id' => $user->id,
                        'company_id' => $user->company_id,
                        'type' => 'passport',
                        'file_path' => $photoPath,
                        'original_name' => basename($photoPath),
                        'mime_type' => 'image/jpeg',
                        'size' => Storage::disk('public')->size($photoPath),
                        'is_verified' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $offset++;
            }

            if ($batch !== []) {
                foreach (array_chunk($batch, 200) as $chunk) {
                    Document::insert($chunk);
                    $done += count($chunk);
                }
                $this->command?->info("  … inserted {$done} document rows");
            }
        });

        $this->command?->info("Done. Inserted ~{$done} document rows. Portraits pool: {$portraitCount}. Shared letter: {$letterPath}");
    }

    /**
     * @return list<string>
     */
    protected function ensurePortraitPool(): array
    {
        $paths = [];

        // randomuser.me hosts 100 men + 100 women stock portraits
        foreach (['men' => 100, 'women' => 100] as $group => $total) {
            for ($i = 0; $i < $total; $i++) {
                $relative = "documents/seeded/passports/{$group}_{$i}.jpg";
                $full = Storage::disk('public')->path($relative);

                if (! is_file($full) || filesize($full) < 1000) {
                    $url = "https://randomuser.me/api/portraits/{$group}/{$i}.jpg";
                    try {
                        $response = Http::timeout(20)->withHeaders([
                            'User-Agent' => 'AkwaabaSeeder/1.0',
                        ])->get($url);

                        if (! $response->successful() || strlen($response->body()) < 1000) {
                            $this->command?->warn("Skip portrait {$group}/{$i}");

                            continue;
                        }

                        Storage::disk('public')->put($relative, $response->body());
                    } catch (\Throwable $e) {
                        $this->command?->warn("Failed {$group}/{$i}: ".$e->getMessage());

                        continue;
                    }
                }

                if (is_file($full) && filesize($full) >= 1000) {
                    $paths[] = $relative;
                }
            }
        }

        // Shuffle assignment variety while remaining deterministic across re-runs of mapping
        sort($paths);

        return $paths;
    }
}
