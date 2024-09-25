<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Extraction>
 */
class ExtractionFactory extends Factory
{
    protected $model = \App\Models\Extraction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'extracted_text' => $this->faker->paragraphs(3, true),
        ];
    }
}
