<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $globalScopeTags = [
            'work',
            'personal',
            'identification',
            'medical',
            'financial',
            'legal',
        ];

        foreach ($globalScopeTags as $tag) {
            Tag::create(['name' => $tag]);
        }

    }
}
