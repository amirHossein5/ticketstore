<?php

namespace Tests\Feature\Dashboard;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function ticket(?User $for = null, bool $published = false, string $created_at = null): Ticket
    {
        return Ticket::factory()->create([
            'user_id' => $for?->id,
            'published_at' => $published ? now()->toDateTimeString() : null,
            'created_at' => $created_at,
        ]);
    }

    /** @test */
    public function user_must_be_verified()
    {
        $this->emailMustBeVerifiedIn('/dashboard');
    }

    /** @test */
    public function shows_list_of_latest_user_created_tickets()
    {
        $user = User::factory()->create();

        $draftA = $this->ticket($user, created_at: '2024-1-1');
        $this->ticket(created_at: '2024-1-2');

        $publishedB = $this->ticket($user, published: true, created_at: '2024-1-3');
        $this->ticket(published: true, created_at: '2024-1-4');

        $draftC = $this->ticket($user, created_at: '2024-1-5');
        $this->ticket(created_at: '2024-1-6');

        $publishedD = $this->ticket($user, published: true, created_at: '2024-1-7');
        $this->ticket(published: true, created_at: '2024-1-7');

        $response = $this->actingAs($user)->get('/dashboard');

        $this->assertEquals(
            [$publishedD->id, $publishedB->id],
            $response['published']->pluck('id')->toArray()
        );
        $this->assertEquals(
            [$draftC->id, $draftA->id],
            $response['drafts']->pluck('id')->toArray()
        );
    }
}
