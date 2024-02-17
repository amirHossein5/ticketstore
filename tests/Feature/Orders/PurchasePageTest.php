<?php

namespace Tests\Feature\Orders;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PurchasePageTest extends TestCase
{
    use RefreshDatabase;

    private function visitPurchaseFor(Ticket $ticket): TestResponse
    {
        return $this->get("/purchase/{$ticket->ulid}");
    }

    /** @test */
    public function shows_the_page()
    {
        $this->publishedTicket();

        $this->visitPurchaseFor($ticket = $this->publishedTicket())
            ->assertOk()
            ->assertViewHas('ticket', $ticket)
            ->assertSee($ticket->title);
    }

    /** @test */
    public function purchasing_sold_out_ticket()
    {
        $this->visitPurchaseFor($this->publishedTicket(['quantity' => 0]))
            ->assertStatus(404);
    }

    /** @test */
    public function purchasing_a_ticket_that_does_not_exists()
    {
        $this->get('/purchase/l3j43k')->assertStatus(404);
    }

    /** @test */
    public function purchasing_an_unpublished_ticket()
    {
        $this->visitPurchaseFor(Ticket::factory()->create())
            ->assertStatus(404);
    }
}
