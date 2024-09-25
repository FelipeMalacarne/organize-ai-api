<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Document;
use App\Models\Extraction;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory(5)->create()->each(function ($user) {
            // Create Documents for each user
            Document::factory(3)->create(['user_id' => $user->id])->each(function ($document) {
                // Create Extractions for each document
                Extraction::factory(2)->create(['document_id' => $document->id]);

                // Attach Tags to each document
                $tags = Tag::factory(5)->create();
                $document->tags()->attach($tags->pluck('id')->toArray());
            });
        });
        $this->call([
            TagSeeder::class,
        ]);
    }
}
