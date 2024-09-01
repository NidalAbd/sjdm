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
        // Fetch a random service
        $service = Service::inRandomOrder()->first();
        $rate = $service ? $service->rate : 1; // Default to 1 if no service found

        // Calculate a realistic charge based on the rate and quantity
        $quantity = $this->faker->numberBetween(1, 100);
        $charge = ($rate * $quantity) / 1000;

        return [
            'user_id' => User::inRandomOrder()->first()->id, // Use an existing user
            'service_id' => $service ? $service->service_id : null, // Use an existing service
            'link' => $this->faker->url,
            'quantity' => $quantity,
            'runs' => $this->faker->optional()->numberBetween(1, 10),
            'charge' => $charge,
            'status' => $this->faker->randomElement(['pending', 'completed', 'canceled', 'partial', 'in progress']),
            'api_order_id' => $this->faker->optional()->randomNumber(),
            'interval' => $this->faker->optional()->numberBetween(1, 30),
            'start_count' => $this->faker->numberBetween(1000, 10000), // Example range for start count
            'remains' => $this->faker->numberBetween(0, 1000), // Example range for remains
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Orders created within the last year
            'updated_at' => now(), // Current time for updated_at
        ];
    }
}
