<?php

namespace Database\Factories;

use App\Models\Box;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Box>
 */
class BoxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'code' => 'BOX'.$this->faker->unique()->numerify('###'),
            'qr_uuid' => (string) Str::uuid(),
            'description' => $this->faker->sentence(),
            'level' => 1,
            'status' => 'packed',
        ];
    }

    public function childOf(Box $parent): static
    {
        return $this->state(fn (array $attributes): array => [
            'parent_id' => $parent->id,
            'level' => $parent->level + 1,
        ]);
    }
}
