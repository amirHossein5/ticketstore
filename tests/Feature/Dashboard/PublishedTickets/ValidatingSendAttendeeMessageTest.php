<?php

namespace Tests\Feature\Dashboard\PublishedTickets;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ValidatingSendAttendeeMessageTest extends TestCase
{
    use RefreshDatabase;

    private function sendRequest(array $overrides = []): TestResponse
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->published()->create();
        $data = array_merge(['title' => 'a title', 'body' => 'a body'], $overrides);

        return $this->actingAs($user)
            ->post("/dashboard/published-tickets/{$ticket->ulid}/attendee-message", $data);
    }

    /** @test */
    public function title_is_required()
    {
        $this->sendRequest(['title' => ''])
            ->onlyHasErrors('title');
    }

    /** @test */
    public function body_is_required()
    {
        $this->sendRequest(['body' => ''])
            ->onlyHasErrors('body');
    }
}
