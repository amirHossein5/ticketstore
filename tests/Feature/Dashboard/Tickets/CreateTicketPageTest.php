<?php

namespace Tests\Feature\Dashboard\Tickets;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTicketPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function verified_users_can_access()
    {
        $this->emailMustBeVerifiedIn('/dashboard/tickets/create');
    }
}
