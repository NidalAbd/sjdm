<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id, // Use an existing user
            'service_id' => Service::inRandomOrder()->first()->service_id, // Use an existing service
            'link' => $this->faker->url,
            'quantity' => $this->faker->numberBetween(1, 100),
            'runs' => $this->faker->optional()->numberBetween(1, 10),
            'charge' => $this->faker->randomFloat(2, 1, 100),
            'status' => $this->faker->randomElement(['pending', 'completed', 'canceled']),
            'api_order_id' => $this->faker->optional()->randomNumber(),
            'interval' => $this->faker->optional()->numberBetween(1, 30),
        ];
    }
}

