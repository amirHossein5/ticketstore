<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{
    use RefreshDatabase;

    private function visitOrder(Order $order): TestResponse
    {
        return $this->get(URL::signedRoute('orders.show', ['order' => $order]));
    }

    /** @test */
    public function shows_order_with_ticket_codes()
    {
        $order = Order::factory()->create();
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();

        $order->tickets()->attach($ticket1->id, [
            'code' => 'ticketcode1',
        ]);
        $order->tickets()->attach($ticket2->id, [
            'code' => 'ticketcode2',
        ]);

        $response = $this->visitOrder($order);

        $this->assertEquals($response['order']->toArray(), $order->load('tickets')->toArray());
        $response->assertSee($order->code);
        $response->assertSee("*** **** **** {$order->last_4}");
        $response->assertSee('$'.number_format($order->charged / 100, 2));
        $response->assertSeeInOrder([
            $ticket1->title,
            $ticket1->formatted_time_to_use,
            'ticketcode1',
            $order->email,

            $ticket2->title,
            $ticket2->formatted_time_to_use,
            'ticketcode2',
            $order->email,
        ]);
    }

    /** @test */
    public function viewing_non_existent_order()
    {
        $this->get(URL::signedRoute('orders.show', ['order' => 'fasdf']))
            ->assertStatus(404);
    }
}
