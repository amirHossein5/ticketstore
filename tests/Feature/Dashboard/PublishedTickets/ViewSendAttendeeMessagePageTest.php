<?php

namespace Tests\Feature\Dashboard\PublishedTickets;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewSendAttendeeMessagePageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_must_be_verified()
    {
        $ticket = Ticket::factory()->create();

        $this->emailMustBeVerifiedIn("/dashboard/published-tickets/{$ticket->ulid}/attendee-message");
    }

    /** @test */
    public function email_must_be_verified_even_when_ticket_not_found()
    {
        $this->emailMustBeVerifiedIn('/dashboard/published-tickets/not-exists/attendee-message');
    }

    /** @test */
    public function cant_view_page_for_another_user_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->published()->create([
            'user_id' => User::factory()
        ]);

        $this->actingAs($user)
            ->get("/dashboard/published-tickets/{$ticket->ulid}/attendee-message")
            ->assertStatus(404);
    }

    /** @test */
    public function cant_view_page_for_unpublished_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create();

        $this->actingAs($user)
            ->get("/dashboard/published-tickets/{$ticket->ulid}/attendee-message")
            ->assertStatus(404);
    }
}
