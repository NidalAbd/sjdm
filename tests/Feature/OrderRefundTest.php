<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class OrderRefundTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_partial_order_can_calculate_refund()
    {
        // Create a user
        $user = User::factory()->create([
            'balance' => 100.00
        ]);

        // Create a service
        $service = Service::factory()->create([
            'rate' => 10.00
        ]);

        // Create a partial order
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->service_id,
            'status' => 'partial',
            'charge' => 100.00,
            'start_count' => 1000,
            'remains' => 500, // 50% completed
            'quantity' => 1000
        ]);

        // Test completion percentage calculation
        $this->assertEquals(50.0, $order->completion_percentage);
        
        // Test refund amount calculation
        $this->assertEquals(50.00, $order->refund_amount);
        
        // Test eligibility for refund
        $this->assertTrue($order->isEligibleForRefund());
    }

    public function test_completed_order_not_eligible_for_refund()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->service_id,
            'status' => 'completed',
            'charge' => 100.00,
            'start_count' => 1000,
            'remains' => 0,
            'quantity' => 1000
        ]);

        $this->assertFalse($order->isEligibleForRefund());
        $this->assertEquals(0, $order->refund_amount);
    }

    public function test_order_with_zero_charge_not_eligible_for_refund()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->service_id,
            'status' => 'partial',
            'charge' => 0.00,
            'start_count' => 1000,
            'remains' => 500,
            'quantity' => 1000
        ]);

        $this->assertFalse($order->isEligibleForRefund());
        $this->assertEquals(0, $order->refund_amount);
    }

    public function test_order_status_color_attribute()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->service_id,
            'status' => 'partial'
        ]);

        $this->assertEquals('warning', $order->status_color);
    }
}
