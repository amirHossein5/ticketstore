<?php

namespace Tests\Feature\Dashboard\PublishedTickets;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ViewPublishedTicketOrdersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_must_be_verified()
    {
        $ticket = Ticket::factory()->create();

        $this->emailMustBeVerifiedIn("/dashboard/published-tickets/{$ticket->ulid}/orders");
    }

    /** @test */
    public function email_must_be_verified_even_when_ticket_not_found()
    {
        $this->emailMustBeVerifiedIn('/dashboard/published-tickets/not-exists/orders');
    }

    /** @test */
    public function page_renders()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->published()->create();

        $response = $this->actingAs($user)->get("/dashboard/published-tickets/{$ticket->ulid}/orders")
            ->assertStatus(200);

        $this->assertEquals($ticket->fresh(), $response['ticket']);
        $this->assertEquals(0.00, $response['soldOutPercentage']);
        $this->assertEquals(0, $response['totalRevenueInDollars']);
    }

    /** @test */
    public function sold_out_percentage_is_calculated_correctly()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->published()->create([
            'sold_count' => 2,
            'quantity' => 5
        ]);

        $response = $this->actingAs($user)->get("/dashboard/published-tickets/{$ticket->ulid}/orders");

        $this->assertEquals(28.57, $response['soldOutPercentage']);
    }

    /** @test */
    public function total_revenue_is_calculated_correctly()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->published()->create();

        Order::factory()->create(['charged' => 25000])->addTicket($ticket, 2);
        Order::factory()->create(['charged' => 25000])->addTicket($ticket, 2);
        Order::factory()->create(['charged' => 25000])->addTicket($ticket, 2);
        Order::factory()->create(['charged' => 25000])->addTicket($ticket, 2);

        $response = $this->actingAs($user)->get("/dashboard/published-tickets/{$ticket->ulid}/orders");

        $this->assertEquals('1,000.00', $response['totalRevenueInDollars']);
    }

    /** @test */
    public function passes_latest_recent_ticket_orders()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->published()->create();
        $anotherTicket = Ticket::factory()->for($user)->published()->create();

        $orderA = Order::factory()->create(['created_at' => Carbon::parse('1 day')])->addTicket($ticket, 3);
        $orderB = Order::factory()->create(['created_at' => Carbon::parse('2 day')])->addTicket($ticket, 3);
        $orderC = Order::factory()->create(['created_at' => Carbon::parse('3 day')])->addTicket($ticket, 3);
        $orderD = Order::factory()->create(['created_at' => Carbon::parse('4 day')])->addTicket($ticket, 3);
        $orderE = Order::factory()->create(['created_at' => Carbon::parse('5 day')])->addTicket($ticket, 3);
        $orderF = Order::factory()->create(['created_at' => Carbon::parse('6 day')])->addTicket($ticket, 3);
        $orderG = Order::factory()->create(['created_at' => Carbon::parse('7 day')])->addTicket($ticket, 3);
        $orderH = Order::factory()->create(['created_at' => Carbon::parse('8 day')])->addTicket($ticket, 3);
        $orderI = Order::factory()->create(['created_at' => Carbon::parse('9 day')])->addTicket($ticket, 3);
        $orderJ = Order::factory()->create(['created_at' => Carbon::parse('10 day')])->addTicket($ticket, 3);
        $orderK = Order::factory()->create(['created_at' => Carbon::parse('11 day')])->addTicket($ticket, 3);
        $orderL = Order::factory()->create(['created_at' => Carbon::parse('12 day')])->addTicket($ticket, 3);

        Order::factory()->create(['created_at' => Carbon::parse('8 day')])->addTicket($anotherTicket, 3);
        Order::factory()->create(['created_at' => Carbon::parse('9 day')])->addTicket($anotherTicket, 3);
        Order::factory()->create(['created_at' => Carbon::parse('10 day')])->addTicket($anotherTicket, 3);
        Order::factory()->create(['created_at' => Carbon::parse('11 day')])->addTicket($anotherTicket, 3);
        Order::factory()->create(['created_at' => Carbon::parse('12 day')])->addTicket($anotherTicket, 3);

        $response = $this->actingAs($user)->get("/dashboard/published-tickets/{$ticket->ulid}/orders");

        $this->assertEquals([
            $orderL->id,
            $orderK->id,
            $orderJ->id,
            $orderI->id,
            $orderH->id,
            $orderG->id,
            $orderF->id,
            $orderE->id,
            $orderD->id,
            $orderC->id,
        ], $response['orders']->pluck('id')->toArray());

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $response['orders']);
    }

    /** @test */
    public function cannot_visit_orders_of_unpublished_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create();

        $this->actingAs($user)->get("/dashboard/published-tickets/{$ticket->ulid}/orders")
            ->assertStatus(404);
    }

    /** @test */
    public function cannot_visit_another_users_ticket_orders()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->published()->create([
            'user_id' => User::factory()
        ]);

        $this->actingAs($user)->get("/dashboard/published-tickets/{$ticket->ulid}/orders")
            ->assertStatus(404);
    }
}
