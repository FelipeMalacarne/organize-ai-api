<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialAccount>
 */
class SocialAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_name' => $this->faker->randomElement(['facebook', 'google', 'linkedin', 'twitter', 'instagram']),
            'provider_id' => $this->faker->unique()->uuid,
            'token' => $this->faker->sha1,
            'refresh_token' => $this->faker->sha1,
            'expires_in' => $this->faker->randomNumber(5),
            'user_id' => User::factory(),
        ];
    }
}
