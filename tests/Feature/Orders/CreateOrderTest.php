<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $override = [], int $maxQuantity = 999): array
    {
        return array_merge([
            'email' => fake()->email,
            'quantity' => fake()->numberBetween(1, $maxQuantity),
            'card_number' => fake()->numerify(1 . str_repeat('#', 15)),
            'exp_month' => fake()->month(),
            'exp_year' => substr(fake()->year(), -2),
            'cvc' => fake()->numberBetween(1000, 9999),
        ], $override);
    }

    /** @test */
    public function creating_order_for_pubslished_ticket()
    {
        $ticket = Ticket::factory()->published()->create();

        $this->assertFalse(Order::exists());

        $response = $this->post(
            "/purchase/{$ticket->ulid}",
            $data = $this->validData(maxQuantity: $ticket->quantity)
        );

        $this->assertTrue(Order::exists());

        $this->assertEquals($ticket->fresh()->quantity, $ticket->quantity - $data['quantity']);
        $this->assertEquals($ticket->fresh()->sold_count, $data['quantity']);

        $order = Order::first();

        $this->assertEquals([
            'id' => 1,
            'email' => $data['email'],
            'quantity' => $data['quantity'],
            'charged' => $ticket->price * $data['quantity'],
            'last_4' => substr($data['card_number'], -4),
        ], collect($order)->except('code', 'created_at', 'updated_at')->toArray());

        $this->assertTrue(Ulid::isValid($order->code));

        $response->assertRedirect("/orders/{$order->code}");
    }

    /** @test */
    public function by_ordering_multiple_tickets_quantity_is_calculated_correctly()
    {
        $ticket = Ticket::factory()->published()->create();

        $oldTicket = $ticket;
        $response = $this->post(
            "/purchase/{$ticket->ulid}",
            $data = $this->validData(maxQuantity: $ticket->quantity)
        );

        $ticket = $ticket->fresh();
        $this->assertEquals($ticket->quantity, $oldTicket->quantity - $data['quantity']);
        $this->assertEquals($ticket->sold_count, $data['quantity']);

        $oldTicket = $ticket;
        $response = $this->post(
            "/purchase/{$ticket->ulid}",
            $data = $this->validData(maxQuantity: $ticket->quantity)
        );

        $ticket = $ticket->fresh();
        $this->assertEquals($ticket->quantity, $oldTicket->quantity - $data['quantity']);
        $this->assertEquals($ticket->sold_count, $oldTicket->sold_count + $data['quantity']);
    }

    /** @test */
    public function creating_order_for_non_existent_ticket()
    {
        $this->post('/purchase/3kljA')->assertStatus(404);
    }

    /** @test */
    public function creating_order_for_unpublished_ticket()
    {
        $ticket = Ticket::factory()->create();

        $this->post("/purchase/{$ticket->ulid}")->assertStatus(404);
    }
}
