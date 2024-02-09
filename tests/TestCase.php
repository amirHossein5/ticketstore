<?php

namespace Tests;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function publishedTicket(array $data = []): Ticket
    {
        return Ticket::factory()->published()->create($data);
    }
}
