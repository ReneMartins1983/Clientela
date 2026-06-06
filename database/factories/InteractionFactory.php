<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Interaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Interaction>
 */
class InteractionFactory extends Factory
{
    protected $model = Interaction::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'type' => $this->faker->randomElement(Interaction::TYPES),
            'notes' => $this->faker->sentence(),
            'happened_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
