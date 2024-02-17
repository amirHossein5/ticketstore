<?php

namespace Tests\Feature\Dashboard\Tickets;

use App\Jobs\ProcessTicketImage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreTicketTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Football A vs B',
            'subtitle' => 'Football game in xyz',
            'price' => '25.00',
            'quantity' => '75',
            'time_to_use' => '2024-12-08 11:28AM',
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

    /** @test */
    public function adding_a_valid_ticket()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/dashboard/tickets', $data = [
            'title' => 'Football A vs B',
            'subtitle' => 'Football game in xyz',
            'price' => '25.00',
            'quantity' => '75',
            'time_to_use' => '2024-12-08 11:28AM',
        ]);

        tap(Ticket::first(), function ($ticket) use ($user, $response) {
            $this->assertDatabaseCount('tickets', 1);

            $this->assertEquals('Football A vs B', $ticket->title);
            $this->assertEquals('Football game in xyz', $ticket->subtitle);
            $this->assertEquals(2500, $ticket->price);
            $this->assertEquals(75, $ticket->quantity);
            $this->assertEquals(Carbon::parse('2024-12-08 11:28AM'), $ticket->time_to_use);
            $this->assertEquals(null, $ticket->image);
            $this->assertTrue($ticket->user->is($user));

            $response->assertRedirect('/dashboard');
        });
    }

    /** @test */
    public function poster_image_is_optimized()
    {
        Storage::fake('public');
        Queue::fake();

        $user = User::factory()->create();

        $this->actingAs($user)->post('/dashboard/tickets', $this->validData([
            'image' => $this->unoptimizedImage(),
        ]));

        Queue::assertPushed(ProcessTicketImage::class, function ($job) {
            $job->handle();

            return true;
        });

        $ticket = Ticket::first();

        $this->assertTrue(
            Storage::disk('public')->exists('ticket-posters/'.basename($ticket->image))
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

        $this->actingAs(User::factory()->create())
            ->post('/dashboard/tickets', $this->validData());

        Queue::assertNotPushed(ProcessTicketImage::class);
    }

    /** @test */
    public function price_will_be_converted_to_cents()
    {
        $user = User::factory()->create();
        $prices = [
            '25123.00' => 2512300,
            '25123' => 2512300,

            '200' => 20000,
            '2.2' => 220,
        ];

        foreach ($prices as $dollars => $cents) {
            $this->actingAs($user)->post('/dashboard/tickets', $this->validData(['price' => $dollars]));

            $this->assertEquals($cents, Ticket::first()->price);

            Ticket::truncate();
        }
    }
}
