<?php

namespace Tests\Feature\Models;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cannot_create_duplicated_ticket_codes()
    {
        $order = Order::factory()->create();
        $ticket1 = Ticket::factory()->create();

        $order->tickets()->attach($ticket1, ['code' => '123']);
        $this->expectException(UniqueConstraintViolationException::class);
        $order->tickets()->attach($ticket1, ['code' => '123']);
    }
}
