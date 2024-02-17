<?php

namespace Tests\Feature\Dashboard\PublishedTickets;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PublishTicketsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_must_be_verified()
    {
        $this->emailMustBeVerifiedIn('/dashboard/published-tickets', 'post');
    }

    /** @test */
    public function can_publish_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create();

        $this->freezeTime(function () use ($user, $ticket) {
            $this->assertEquals(null, $ticket->published_at);

            $this->actingAs($user)->post('/dashboard/published-tickets', [
                'ticket' => $ticket->ulid,
            ])->assertRedirect('/dashboard');

            $this->assertEquals(now()->toDateTimeString(), $ticket->fresh()->published_at->toDateTimeString());
        });
    }

    /** @test */
    public function publishing_non_existent_ticket()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/dashboard/published-tickets', [
            'ticket' => 'invalid ulid',
        ])->assertStatus(404);

        $this->assertDatabaseCount('tickets', 0);
    }

    /** @test */
    public function publishing_another_user_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => User::factory(),
        ]);

        $this->actingAs($user)->post('/dashboard/published-tickets', [
            'ticket' => $ticket->ulid,
        ])->assertStatus(404);

        $this->assertTrue($ticket->published_at === null);
    }
}
