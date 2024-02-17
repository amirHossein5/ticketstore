<?php

namespace Tests\Feature\Orders;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class StoreOrderValidationTest extends TestCase
{
    use RefreshDatabase;

    private Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ticket = $this->publishedTicket();
    }

    private function validData(array $override = []): array
    {
        return array_merge([
            'email' => fake()->email,
            'quantity' => $q = fake()->numberBetween(1, $this->ticket->fresh()->quantity),
            'card_number' => fake()->numerify(1 .str_repeat('#', 15)),
            'exp_month' => fake()->month(),
            'exp_year' => substr(fake()->year(), -2),
            'cvc' => fake()->numberBetween(1000, 9999),
        ], $override);
    }

    private function sendRequest(array $overrides): TestResponse
    {
        return $this->post("/purchase/{$this->ticket->ulid}", $this->validData($overrides));
    }

    /** @test */
    public function email_is_required()
    {
        $this->sendRequest([
            'email' => '',
        ])->onlyHasErrors('email');
    }

    /** @test */
    public function email_must_be_valid()
    {
        $this->sendRequest([
            'email' => 'aslfjksdklj.com',
        ])->onlyHasErrors('email');
    }

    /** @test */
    public function quantity_is_required()
    {
        $this->sendRequest([
            'quantity' => '',
        ])->onlyHasErrors('quantity');
    }

    /** @test */
    public function quantity_cannot_be_string()
    {
        $this->sendRequest([
            'quantity' => 'e2',
        ])->onlyHasErrors('quantity');
    }

    /** @test */
    public function quantity_cannot_be_numeric()
    {
        $this->sendRequest([
            'quantity' => '3.3',
        ])->onlyHasErrors('quantity');
    }

    /** @test */
    public function quantity_cannot_be_less_than_one()
    {
        $this->sendRequest([
            'quantity' => '0',
        ])->onlyHasErrors('quantity');

        $this->sendRequest([
            'quantity' => '-1',
        ])->onlyHasErrors('quantity');

        $this->sendRequest([
            'quantity' => '1',
        ])->assertSessionHasNoErrors();
    }

    /** @test */
    public function quantity_cannot_be_more_than_ticket_quantity()
    {
        $this->sendRequest([
            'quantity' => ''.$this->ticket->quantity + 1,
        ])->onlyHasErrors('quantity');

        $this->sendRequest([
            'quantity' => "{$this->ticket->quantity}",
        ])->assertSessionHasNoErrors();
    }

    /** @test */
    public function card_number_is_required()
    {
        $this->sendRequest([
            'card_number' => '',
        ])->onlyHasErrors('card_number');
    }

    /** @test */
    public function card_number_is_int()
    {
        $this->sendRequest([
            'card_number' => '424242424242424a',
        ])->onlyHasErrors('card_number');
    }

    /** @test */
    public function card_number_is_16_digits_long()
    {
        $this->sendRequest([
            'card_number' => '4242',
        ])->onlyHasErrors('card_number');

        $this->sendRequest([
            'card_number' => str_repeat('4242', 5),
        ])->onlyHasErrors('card_number');

        $this->sendRequest([
            'card_number' => str_repeat('4242', 4),
        ])->assertSessionHasNoErrors();
    }

    /** @test */
    public function exp_month_is_required()
    {
        $this->sendRequest([
            'exp_month' => '',
        ])->onlyHasErrors('exp_month');
    }

    /** @test */
    public function exp_month_is_numeric()
    {
        $this->sendRequest([
            'exp_month' => 's2',
        ])->onlyHasErrors('exp_month');
    }

    /** @test */
    public function exp_month_is_2_digits_long()
    {
        $this->sendRequest([
            'exp_month' => '102',
        ])->onlyHasErrors('exp_month');

        $this->sendRequest([
            'exp_month' => '01',
        ])->assertSessionHasNoErrors();
    }

    /** @test */
    public function exp_year_is_required()
    {
        $this->sendRequest([
            'exp_year' => '',
        ])->onlyHasErrors('exp_year');
    }

    /** @test */
    public function exp_year_is_numeric()
    {
        $this->sendRequest([
            'exp_year' => 's2',
        ])->onlyHasErrors('exp_year');
    }

    /** @test */
    public function exp_year_is_2_digits_long()
    {
        $this->sendRequest([
            'exp_year' => '102',
        ])->onlyHasErrors('exp_year');

        $this->sendRequest([
            'exp_year' => '01',
        ])->assertSessionHasNoErrors();
    }

    /** @test */
    public function cvc_is_required()
    {
        $this->sendRequest([
            'cvc' => '',
        ])->onlyHasErrors('cvc');
    }

    /** @test */
    public function cvc_is_numeric()
    {
        $this->sendRequest([
            'cvc' => 'a422',
        ])->onlyHasErrors('cvc');
    }

    /** @test */
    public function cvc_is_3_to_4_digits_long()
    {
        $this->sendRequest([
            'cvc' => '10',
        ])->onlyHasErrors('cvc');

        $this->sendRequest([
            'cvc' => '10101',
        ])->onlyHasErrors('cvc');

        $this->sendRequest([
            'cvc' => '011',
        ])->assertSessionHasNoErrors();

        $this->sendRequest([
            'cvc' => '1011',
        ])->assertSessionHasNoErrors();
    }
}
