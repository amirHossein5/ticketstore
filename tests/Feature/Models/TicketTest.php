<?php

namespace Tests\Feature\Models;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function generates_ulid_alongside_of_id()
    {
        $ticket = Ticket::factory()->create();

        $this->assertTrue(is_integer($ticket->id));
        $this->assertTrue(Ulid::isValid($ticket->ulid));
    }

    /** @test */
    public function formats_time_to_use()
    {
        $ticket = Ticket::factory()->create();

        $this->assertEquals(
            $ticket->time_to_use->format('D M d h:iA'),
            $ticket->formatted_time_to_use,
        );
    }

    /** @test */
    public function formats_price_from_cents_to_dollars()
    {
        $ticket = Ticket::factory()->create();

        $this->assertEquals(
            number_format($ticket->price / 100, 2),
            $ticket->formatted_price,
        );
    }
}
