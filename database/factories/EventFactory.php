<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_name' => $this->faker->sentence,
            'event_descriptions' => $this->faker->paragraph,
            'thumbnail' => $this->faker->imageUrl(),
            'date' => $this->faker->dateTimeBetween('+0 days', '+1 year'),
            'location' => [
                'city' => $this->faker->city,
                'latitude' => $this->faker->latitude,
                'longitude' => $this->faker->longitude,
            ],
            'total_ticket' => 100,
            'available_ticket' => 100,
            'event_status_id' => 1,
            'category_id' => rand(1, 7),
            'price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
