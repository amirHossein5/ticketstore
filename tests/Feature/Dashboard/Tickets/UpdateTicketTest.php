<?php

namespace Tests\Feature\Dashboard\Tickets;

use App\Jobs\ProcessTicketImage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateTicketTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'new title',
            'subtitle' => 'new subtitle',
            'time_to_use' => '2024-1-1 10:10AM',
            'price' => '9.00',
            'quantity' => '100',
        ], $overrides);
    }

    private function optimizedImagePath(): string
    {
        return base_path('tests/__fixtures__/optimized.png');
    }

    private function unoptimizedImagePath(): string
    {
        return base_path('tests/__fixtures__/unoptimized.png');
    }

    private function unoptimizedImage(): File
    {
        return File::createWithContent('image.png', file_get_contents($this->unoptimizedImagePath()));
    }

    private function optimizedImage(): File
    {
        return File::createWithContent('image.png', file_get_contents($this->optimizedImagePath()));
    }

    private function image(): File
    {
        return File::image('image.png', 400, 240);
    }

    /** @test */
    public function email_must_be_verified()
    {
        $ticket = Ticket::factory()->create();

        $this->emailMustBeVerifiedIn("/dashboard/tickets/edit/{$ticket->ulid}", 'put');
    }

    /** @test */
    public function email_must_be_verified_even_when_ticket_not_found()
    {
        $this->emailMustBeVerifiedIn('/dashboard/tickets/edit/non-existent-ticket', 'put');
    }

    /** @test */
    public function updating_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create();

        $this->actingAs($user)->put("/dashboard/tickets/edit/{$ticket->ulid}", $data = [
            'title' => 'new title',
            'subtitle' => 'new subtitle',
            'time_to_use' => '2024-1-1 10:10AM',
            'price' => '9.00',
            'quantity' => '100',
        ]);

        tap($ticket->fresh(), function ($ticket) {
            $this->assertEquals('new title', $ticket->title);
            $this->assertEquals('new subtitle', $ticket->subtitle);
            $this->assertEquals(Carbon::parse('2024-1-1 10:10AM'), $ticket->time_to_use);
            $this->assertEquals(900, $ticket->price);
            $this->assertEquals(100, $ticket->quantity);
            $this->assertEquals(null, $ticket->image);
        });
    }

    /** @test */
    public function adding_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create(['image' => null]);

        $this->actingAs($user)->put("/dashboard/tickets/edit/{$ticket->ulid}", $data = $this->validData([
            'image' => $this->unoptimizedImage(),
        ]));

        tap($ticket->fresh(), function ($ticket) {
            $this->assertTrue(
                Storage::disk('public')->exists('ticket-posters/' . basename($ticket->image))
            );

            $this->assertFileEquals(
                $this->optimizedImagePath(),
                Storage::disk('public')->path($ticket->image)
            );
        });
    }

    /** @test */
    public function updating_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create([
            'image' => Storage::disk('public')->put('ticket-posters/', $this->image())
        ]);

        $this->assertTrue(Storage::disk('public')->exists('ticket-posters/' . basename($ticket->image)));

        $this->actingAs($user)->put("/dashboard/tickets/edit/{$ticket->ulid}", $data = $this->validData([
            'image' => $this->unoptimizedImage(),
        ]));

        $this->assertFalse(Storage::disk('public')->exists('ticket-posters/' . basename($ticket->image)));

        tap($ticket->fresh(), function ($ticket) {
            $this->assertTrue(
                Storage::disk('public')->exists('ticket-posters/' . basename($ticket->image))
            );

            $this->assertFileEquals(
                $this->optimizedImagePath(),
                Storage::disk('public')->path($ticket->image)
            );
        });
    }

    /** @test */
    public function removing_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create([
            'image' => Storage::disk('public')->put('ticket-posters/', $this->image()),
        ]);

        $this->actingAs($user)->put("/dashboard/tickets/edit/{$ticket->ulid}", $data = $this->validData([
            'image' => null
        ]));

        $this->assertFalse(Storage::disk('public')->exists('ticket-posters/' . basename($ticket->image)));
        $this->assertNull($ticket->fresh()->image);
    }

    /** @test */
    public function poster_image_is_optimized()
    {
        Storage::fake('public');
        Queue::fake();

        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create();

        $this->actingAs($user)->put("/dashboard/tickets/edit/{$ticket->ulid}", $this->validData([
            'image' => $this->unoptimizedImage(),
        ]));

        Queue::assertPushed(ProcessTicketImage::class, function ($job) {
            $job->handle();
            return true;
        });

        $ticket = $ticket->fresh();

        $this->assertTrue(
            Storage::disk('public')->exists('ticket-posters/' . basename($ticket->image))
        );

        $this->assertFileEquals(
            $this->optimizedImagePath(),
            Storage::disk('public')->path($ticket->image)
        );
    }

    /** @test */
    public function when_no_image_is_sent_no_job_process_image_job_will_dispatch()
    {
        Queue::fake();

        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->create();

        $this->actingAs($user)
            ->put("/dashboard/tickets/edit/{$ticket->ulid}", $this->validData());

        Queue::assertNotPushed(ProcessTicketImage::class);
    }

    /** @test */
    public function cannot_update_published_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user)->published()->create();

        $this->assertEquals($user->id, $ticket->user_id);

        $this->actingAs($user)->put("/dashboard/tickets/edit/{$ticket->ulid}", $this->validData())
            ->assertStatus(404);
    }

    /** @test */
    public function updating_another_user_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => User::factory()
        ]);

        $this->actingAs($user)->put("/dashboard/tickets/edit/{$ticket->ulid}", $this->validData())
            ->assertStatus(404);
    }
}
