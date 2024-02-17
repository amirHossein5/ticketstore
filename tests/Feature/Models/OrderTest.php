<?php

namespace Tests\Feature\Models;

use App\Models\Order;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cannot_create_duplicated_order_codes()
    {
        $order1 = Order::factory()->create();
        $order2 = Order::factory()->create();

        $this->expectException(UniqueConstraintViolationException::class);

        $order1->update(['code' => $order2->code]);
    }

    /** @test */
    public function formats_charged_to_dollars()
    {
        $order = Order::factory()->create(['charged' => 1331]);

        $this->assertEquals(13.31, $order->charged_in_dollars);
    }
}
