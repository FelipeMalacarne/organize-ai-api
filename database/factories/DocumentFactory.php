<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    protected $model = \App\Models\Document::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence,
            'file_path' => 'uploads/documents/'.Str::uuid().'.pdf',
            'file_type' => 'pdf',
            'metadata' => [
                'category' => $this->faker->randomElement(['invoice', 'contract', 'resume']),
                'language' => $this->faker->randomElement(['en', 'es', 'fr']),
                'source' => 'user_upload',
            ],
        ];
    }
}
