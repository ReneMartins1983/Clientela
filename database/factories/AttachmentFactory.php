<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => 'documento.pdf',
            'path' => 'attachments/'.$this->faker->uuid().'.pdf',
            'size' => $this->faker->numberBetween(1000, 500000),
            'mime' => 'application/pdf',
        ];
    }
}
