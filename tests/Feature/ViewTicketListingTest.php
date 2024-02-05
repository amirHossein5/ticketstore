<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class ViewTicketListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_list_of_tickets_ordered_by_created_at_desc(): void
    {
        $expected = Ticket::factory(3)->create();

        $response = $this->get('/');

        $response->assertViewHas('tickets', $expected->sortByDesc('created_at'));
        $response->assertSeeInOrder($expected->pluck('ulid')->toArray());
        $response->assertSeeInOrder($expected->pluck('title')->toArray());
    }

    /** @test */
    public function shows_a_message_when_no_tickets_exists()
    {
        $this->get('/')->assertSee('No tickets found!');

        Ticket::factory()->create();

        $this->get('/')->assertDontSee('No tickets found!');
    }
}
