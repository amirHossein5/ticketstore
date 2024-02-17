<?php

namespace Tests\Feature;

use App\Mail\InvitePromoterMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AddingPromoterViaCliTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function adding_promoter()
    {
        Mail::fake();

        $this->artisan('add-promoter', ['email' => $email = fake()->email])
            ->assertSuccessful();

        $this->assertDatabaseCount('invitations', 1);

        $invitation = Invitation::first();
        $this->assertEquals($email, $invitation->email);

        Mail::assertQueuedCount(1);
        Mail::assertQueued(InvitePromoterMail::class, function (InvitePromoterMail $mail) use ($email, $invitation) {
            $mail->assertSeeInOrderInHtml([
                'Ticketstore registration link:',
                "{$invitation->register_link}",
                "Link will expire at {$invitation->created_at->addMinutes(30)->format('H:i')}",
            ]);
            $mail->assertTo($email);
            $mail->assertFrom(config('mail.from.address'));
            $mail->assertHasSubject('Promoter invitation');

            return true;
        });
    }

    /** @test */
    public function register_link_expires()
    {
        Mail::fake();

        $this->freezeTime(function () {
            $this->artisan('add-promoter', ['email' => fake()->email]);

            $invitation = Invitation::first();

            $this->get($invitation->register_link)->assertOk();
            $this->travel(31)->minutes();
            $this->get($invitation->register_link)->assertNotFound();
        });
    }

    /** @test */
    public function cannot_use_invalid_email()
    {
        Mail::fake();

        $this->artisan('add-promoter', ['email' => 'invalid-email'])
            ->expectsOutput('email is invalid')
            ->assertFailed();

        $this->assertDatabaseCount('invitations', 0);
        Mail::assertNothingSent();
    }

    /** @test */
    public function cannot_invite_someone_with_existing_email()
    {
        Mail::fake();
        User::factory()->create(['email' => 'some@email.com']);

        $this->artisan('add-promoter', ['email' => 'some@email.com'])
            ->expectsOutput('email already exists')
            ->assertFailed();

        $this->assertDatabaseCount('invitations', 0);
        Mail::assertNothingSent();
    }
}
