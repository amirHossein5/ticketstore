<?php

namespace Tests\Feature\Orders;

use App\Mail\OrderCreatedMail;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class StoreOrderTest extends TestCase
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

    private function purchase(Ticket $ticket, array $data = []): array
    {
        $response = $this->post(
            "/purchase/{$ticket->ulid}",
            $form = $this->validData($data, $ticket->fresh()->quantity)
        );

        return [$form, $response];
    }

    /** @test */
    public function creating_order_for_pubslished_ticket()
    {
        Mail::fake();

        $ticket = $this->publishedTicket();
        $this->assertDatabaseCount('orders', 0);

        [$data, $response] = $this->purchase($ticket);

        $this->assertDatabaseCount('orders', 1);

        $order = Order::first();
        $this->assertStringStartsWith(
            url("/orders/{$order->code}"),
            $response->headers->get('Location')
        );

        $this->assertEquals([
            'id' => 1,
            'email' => $data['email'],
            'quantity' => $data['quantity'],
            'charged' => $ticket->price * $data['quantity'],
            'last_4' => substr($data['card_number'], -4),
        ], collect($order)->except('code', 'created_at', 'updated_at')->toArray());

        $this->assertTrue(Ulid::isValid($order->code));

        Mail::assertQueuedCount(1);
        Mail::assertQueued(OrderCreatedMail::class, function ($mail) use ($order, $response, $data) {
            $mail->assertSeeInOrderInHtml([
                "Your order link:",
                $response->headers->get('Location'),
                "Link will expire at {$order->created_at->addMinutes(30)->format('H:i')}"
            ]);
            $mail->assertTo($data['email']);
            $mail->assertFrom(config('mail.from.address'));
            $mail->assertHasSubject('Your Order Link');

            return true;
        });
    }

    /** @test */
    public function by_ordering_multiple_tickets_quantity_is_calculated_correctly()
    {
        $ticket = $this->publishedTicket(['quantity' => 10]);

        $this->purchase($ticket, ['quantity' => 4]);

        $this->assertEquals(4, $ticket->fresh()->sold_count);
        $this->assertEquals(6, $ticket->fresh()->quantity);

        $this->purchase($ticket, ['quantity' => 6]);

        $this->assertEquals(10, $ticket->fresh()->sold_count);
        $this->assertEquals(0, $ticket->fresh()->quantity);
    }

    /**
     * @group slow
     *
     * @test
     */
    public function creates_unique_ticket_codes()
    {
        $ticket = $this->publishedTicket(['quantity' => $tries = 20000]);

        $this->purchase($ticket, ['quantity' => $tries]);

        $this->assertEquals(
            $tries,
            Order::first()->tickets()->pluck('code')->unique()->count()
        );
    }

    /** @test */
    public function getting_repeated_random_code()
    {
        $GLOBALS['codes'] = [
            'code1',
            'code1', 'code1', 'code2',
        ];

        app()->bind('short_code', function () {
            return array_shift($GLOBALS['codes']);
        });

        $ticket = $this->publishedTicket(['quantity' => 10]);

        $this->purchase($ticket, ['quantity' => 2]);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_ticket', 2);
        $this->assertEquals(
            ['code1', 'code2'],
            Order::first()->tickets()->pluck('code')->toArray()
        );
    }

    /** @test */
    public function order_show_page_will_expire()
    {
        $ticket = $this->publishedTicket();

        $this->freezeTime(function () use ($ticket) {
            [, $response] = $this->purchase($ticket);

            $url = $response->headers->get('Location');

            $this->get($url)
                ->assertOk()
                ->assertSee('This page expires in ' . now()->addMinutes(30)->format('H:i'));
            $this->travel(31)->minutes();
            $this->get($url)->assertStatus(404);
        });
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
