<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $admin = User::inRandomOrder()->where('role', User::ADMIN_ROLE)->first();

        return [
            'admin_id' => $admin->id,
            'name' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 10, 500), // Random price between 10 and 500
            'description' => $this->faker->sentence(),
            'stock' => $this->faker->randomNumber(2)
        ];
    }
}
