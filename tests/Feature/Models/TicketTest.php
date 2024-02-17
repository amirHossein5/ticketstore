<?php

namespace Tests\Feature\Models;

use App\Models\Ticket;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cannot_create_duplicated_ulids()
    {
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();

        $this->expectException(UniqueConstraintViolationException::class);

        $ticket1->update(['ulid' => $ticket2->ulid]);
    }

    /** @test */
    public function generates_ulid_alongside_of_id()
    {
        $ticket = Ticket::factory()->create();

        $this->assertTrue(is_int($ticket->id));
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

    /** @test */
    public function determine_ticket_sold_out()
    {
        $ticket = Ticket::factory()->create(['quantity' => 1]);

        $this->assertFalse($ticket->sold_out);

        $ticket->update(['quantity' => 0]);

        $this->assertTrue($ticket->sold_out);
    }

    /** @test */
    public function published_scope_test()
    {
        Ticket::factory()->create();
        $this->assertEquals(0, Ticket::published()->count());

        Ticket::factory()->create(['published_at' => now()->addSeconds(5)]);
        $this->assertEquals(0, Ticket::published()->count());

        $ticket = Ticket::factory()->create(['published_at' => now()]);
        $this->assertEquals([$ticket->fresh()->toArray()], Ticket::published()->get()->toArray());
    }

    /** @test */
    public function determining_ticket_is_published()
    {
        $ticket = Ticket::factory()->create();
        $this->assertTrue($ticket->isPublished() === false);

        $ticket = Ticket::factory()->create(['published_at' => now()->addSeconds(5)]);
        $this->assertTrue($ticket->isPublished() === false);

        $ticket = Ticket::factory()->create(['published_at' => now()]);
        $this->assertTrue($ticket->isPublished() === true);
    }

    /** @test */
    public function calculates_total_tickets_count()
    {
        $ticket = Ticket::factory()->create([
            'sold_count' => 10,
            'quantity' => 2,
        ]);

        $this->assertEquals(12, $ticket->totalTicketsCount());
    }
}
