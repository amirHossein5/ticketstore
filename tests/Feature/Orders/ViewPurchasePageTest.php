<?php

namespace Tests\Feature\Orders;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewPurchasePageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_the_page()
    {
        $ticket = Ticket::factory()->published()->create();
        $quantity = fake()->randomDigit();

        $this->get("/purchase/{$ticket->ulid}?quantity={$quantity}")
            ->assertOk()
            ->assertViewHas('quantity', $quantity)
            ->assertViewHas('ticket', $ticket)
            ->assertSee("{$quantity} tickets to {$ticket->title}");
    }

    /** @test */
    public function purchasing_a_ticket_that_does_not_exists()
    {
        $this->get('/purchase/l3j43k')->assertStatus(404);
    }

    /** @test */
    public function purchasing_an_unpublished_ticket()
    {
        $ticket = Ticket::factory()->create();

        $this->get("/purchase/{$ticket->ulid}")->assertStatus(404);
    }

    /** @test */
    public function purchasing_with_invalid_quantity()
    {
        $ticket = Ticket::factory()->published()->create();

        $this->get("/purchase/{$ticket->ulid}")
            ->assertRedirect("/purchase/{$ticket->ulid}?quantity=1");

        $this->get("/purchase/{$ticket->ulid}?quantity=")
            ->assertRedirect("/purchase/{$ticket->ulid}?quantity=1");

        $this->get("/purchase/{$ticket->ulid}?quantity=a2")
            ->assertRedirect("/purchase/{$ticket->ulid}?quantity=1");
    }
}
