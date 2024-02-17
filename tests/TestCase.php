<?php

namespace Tests;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        TestResponse::macro('onlyHasErrors', function (string ...$keys) {
            Assert::assertEquals(
                $keys,
                session('errors')?->getBag('default')->keys(),
                'Failed asserting that session has given errors'
            );

            return $this;
        });
    }

    protected function emailMustBeVerifiedIn(string $route, string $method = 'get'): void
    {
        $this->{$method}($route)->assertRedirect('/login');

        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)->{$method}($route)->assertRedirect('/verify-email');
    }

    protected function publishedTicket(array $data = []): Ticket
    {
        return Ticket::factory()->published()->create($data);
    }
}
