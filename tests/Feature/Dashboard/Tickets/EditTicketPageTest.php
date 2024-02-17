<?php

namespace Tests\Feature\Dashboard\Tickets;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditTicketPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_must_be_verified()
    {
        $ticket = Ticket::factory()->create();

        $this->emailMustBeVerifiedIn("/dashboard/tickets/edit/{$ticket->ulid}");
    }

    /** @test */
    public function email_must_be_verified_even_when_ticket_not_found()
    {
        $this->emailMustBeVerifiedIn('/dashboard/tickets/edit/non-existent-ticket');
    }

    /** @test */
    public function page_renders()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create();

        $response = $this->actingAs($user)->get("/dashboard/tickets/edit/{$ticket->ulid}")
            ->assertStatus(200);

        $this->assertEquals($ticket->fresh(), $response['ticket']);
    }

    /** @test */
    public function cannot_update_published_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->published()->create();

        $this->assertEquals($user->id, $ticket->user_id);

        $this->actingAs($user)->get("/dashboard/tickets/edit/{$ticket->ulid}")
            ->assertStatus(404);
    }

    /** @test */
    public function updating_another_user_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => User::factory(),
        ]);

        $this->actingAs($user)->get("/dashboard/tickets/edit/{$ticket->ulid}")
            ->assertStatus(404);
    }
}
