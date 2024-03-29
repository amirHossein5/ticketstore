<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewTicketListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_published_tickets_in_order(): void
    {
        $expected = Ticket::factory(3)->published()->create()
            ->sortByDesc('created_at');

        $response = $this->get('/');

        $this->assertEquals(
            $response['tickets']->pluck('id'),
            $expected->pluck('id')
        );
        $response->assertSeeInOrder($expected->pluck('ulid')->toArray());
        $response->assertSeeInOrder($expected->pluck('title')->toArray());
    }

    /** @test */
    public function does_not_show_unpublished_tickets()
    {
        Ticket::factory()->create();
        $response = $this->get('/');
        $this->assertEquals([], $response['tickets']->toArray());

        Ticket::factory()->create(['published_at' => now()->addMinute()]);
        $response = $this->get('/');
        $this->assertEquals([], $response['tickets']->toArray());

        $ticket = Ticket::factory()->create(['published_at' => now()]);
        $response = $this->get('/');
        $this->assertEquals([$ticket->fresh()->toArray()], $response['tickets']->toArray());
    }

    /** @test */
    public function shows_a_message_when_no_tickets_exists()
    {
        $this->get('/')->assertSee('No tickets found!');

        Ticket::factory()->published()->create();

        $this->get('/')->assertDontSee('No tickets found!');
    }
}
