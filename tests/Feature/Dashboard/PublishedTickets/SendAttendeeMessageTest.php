<?php

namespace Tests\Feature\Dashboard\PublishedTickets;

use App\Mail\AttendeeMessageMail;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendAttendeeMessageTest extends TestCase
{
    use RefreshDatabase;

    private function assertMailTo(array $emails, $message): void
    {
        foreach ($emails as $email) {
            Mail::assertQueued(AttendeeMessageMail::class, function (AttendeeMessageMail $mail) use ($message, $email) {
                $mail->assertFrom(config('mail.from.address'));
                $mail->hasSubject($message->title);
                $this->assertEquals($message->body, trim($mail->render()));

                return $mail->hasTo($email);
            });
        }
    }

    private function assertMailNotTo(string $email): void
    {
        Mail::assertNotQueued(AttendeeMessageMail::class, function (AttendeeMessageMail $mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    /** @test */
    public function email_must_be_verified()
    {
        $ticket = Ticket::factory()->create();

        $this->emailMustBeVerifiedIn("/dashboard/published-tickets/{$ticket->ulid}/attendee-message", 'post');
    }

    /** @test */
    public function email_must_be_verified_even_when_ticket_not_found()
    {
        $this->emailMustBeVerifiedIn('/dashboard/published-tickets/not-exists/attendee-message', 'post');
    }

    /** @test */
    public function sends_message_to_all_attendees()
    {
        Mail::fake();

        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->published()->create();
        $anotherTicket = Ticket::factory()->for($user)->published()->create();

        Order::factory()->create(['email' => 'a@email.com'])->addTicket($ticket, 10);
        Order::factory()->create(['email' => 'b@email.com'])->addTicket($ticket, 3);
        Order::factory()->create(['email' => 'c@email.com'])->addTicket($anotherTicket, 5);
        Order::factory()->create(['email' => 'd@email.com'])->addTicket($ticket, 3);

        $this->actingAs($user)->post("/dashboard/published-tickets/{$ticket->ulid}/attendee-message", [
            'title' => 'Ticket time has changed',
            'body' => 'Ticket time has changed to new date.',
        ])->assertRedirect('/dashboard')
            ->assertSessionHas('message', 'Message sent successfully');

        $this->assertDatabaseCount('attendee_messages', 1);

        tap($ticket->attendeeMessages()->first(), function ($message) {
            $this->assertEquals($message->title, 'Ticket time has changed');
            $this->assertEquals($message->body, 'Ticket time has changed to new date.');

            Mail::assertQueuedCount(3);
            $this->assertMailTo(['a@email.com', 'b@email.com', 'd@email.com'], $message);
            $this->assertMailNotTo('c@email.com');
        });
    }

    /** @test */
    public function cant_send_message_for_another_users_ticket()
    {
        Mail::fake();

        $user = User::factory()->create();
        $ticket = Ticket::factory()->published()->create([
            'user_id' => User::factory()
        ]);

        Order::factory()->create(['email' => 'a@email.com'])->addTicket($ticket, 10);

        $this->actingAs($user)
            ->post("/dashboard/published-tickets/{$ticket->ulid}/attendee-message")
            ->assertStatus(404);

        Mail::assertQueuedCount(0);
    }

    /** @test */
    public function cant_send_message_for_unpublished_ticket()
    {
        Mail::fake();

        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create();

        Order::factory()->create(['email' => 'a@email.com'])->addTicket($ticket, 10);

        $this->actingAs($user)
            ->post("/dashboard/published-tickets/{$ticket->ulid}/attendee-message")
            ->assertStatus(404);

        Mail::assertQueuedCount(0);
    }
}
