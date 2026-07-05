<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $institutions = require __DIR__.'/data/ghana_universities.php';

        usort($institutions, fn (array $a, array $b) => strcasecmp($a['name'], $b['name']));

        foreach ($institutions as $institution) {
            University::updateOrCreate(
                ['name' => $institution['name']],
                ['region' => $institution['region']],
            );
        }
    }
}
