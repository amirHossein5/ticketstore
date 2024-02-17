<?php

namespace Tests\Feature\Dashboard\Tickets;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class StoreTicketValidationTest extends TestCase
{
    use RefreshDatabase;

    private function sendRequest(array $data): TestResponse
    {
        return $this->actingAs(User::factory()->create())
            ->post('dashboard/tickets', $this->validData($data));
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'title' => fake()->words(5, true),
            'subtitle' => fake()->words(4, true),
            'price' => fake()->numberBetween(100, 999),
            'quantity' => fake()->numberBetween(1, 1000),
            'published_at' => fake()->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d H:i:s'),
            'time_to_use' => fake()->dateTimeBetween('+31 minutes', '+1 year')->format('Y-m-d H:i:s'),
        ], $overrides);
    }

    private function image(string $name = 'image.png', int $width = 400, ?int $height = null): File
    {
        return File::image($name, $width, $height ? $height : $width * 3 / 5); // aspect ratio: 5/3
    }

    /** @test */
    public function email_must_be_verified()
    {
        $this->emailMustBeVerifiedIn('/dashboard/tickets', 'post');
    }

    /** @test */
    public function title_is_required()
    {
        $this->sendRequest(['title' => ''])
            ->onlyHasErrors('title');
    }

    /** @test */
    public function title_has_max_character()
    {
        $this->sendRequest(['title' => str_repeat('a', 256)])
            ->onlyHasErrors('title');
        $this->sendRequest(['title' => str_repeat('a', 255)])
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function subtitle_is_required()
    {
        $this->sendRequest(['subtitle' => ''])
            ->onlyHasErrors('subtitle');
    }

    /** @test */
    public function subtitle_has_max_character()
    {
        $this->sendRequest(['subtitle' => str_repeat('a', 256)])
            ->onlyHasErrors('subtitle');
        $this->sendRequest(['subtitle' => str_repeat('a', 255)])
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function price_is_required()
    {
        $this->sendRequest(['price' => ''])
            ->onlyHasErrors('price');
    }

    /** @test */
    public function price_is_numeric()
    {
        $this->sendRequest(['price' => 'price'])
            ->onlyHasErrors('price');

        $this->sendRequest(['price' => '1.2.00'])
            ->onlyHasErrors('price');

        $this->sendRequest(['price' => '$20'])
            ->onlyHasErrors('price');

        $this->sendRequest(['price' => '1,200'])
            ->onlyHasErrors('price');
    }

    /** @test */
    public function price_is_greater_than_one_dollar()
    {
        $list = [
            '0.9',
            '0.99',
            '.9',
            '0',
        ];

        foreach ($list as $price) {
            $this->sendRequest(['price' => $price])
                ->onlyHasErrors('price')
                ->assertSessionHasErrors(['price' => 'Price must be greater than one dollars.']);
        }
    }

    /** @test */
    public function quantity_is_required()
    {
        $this->sendRequest(['quantity' => ''])
            ->onlyHasErrors('quantity');
    }

    /** @test */
    public function quantity_must_be_integer()
    {
        $this->sendRequest(['quantity' => '1.2'])
            ->onlyHasErrors('quantity');

        $this->sendRequest(['quantity' => '1'])
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function quantity_min_rule()
    {
        $this->sendRequest(['quantity' => '-1'])
            ->onlyHasErrors('quantity');

        $this->sendRequest(['quantity' => '0'])
            ->onlyHasErrors('quantity');

        $this->sendRequest(['quantity' => '1'])
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function time_to_use_is_required()
    {
        $this->sendRequest(['time_to_use' => ''])
            ->onlyHasErrors('time_to_use');
    }

    /** @test */
    public function time_to_use_is_date()
    {
        $this->sendRequest(['time_to_use' => 'some date'])
            ->onlyHasErrors('time_to_use');

        $this->sendRequest(['time_to_use' => now()->addMonth()])
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function image_is_optional()
    {
        $this->sendRequest(['image' => ''])
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function image_is_image()
    {
        Storage::fake('public');

        $this->sendRequest(['image' => 'image.png'])
            ->onlyHasErrors('image')
            ->assertSessionHasErrors(['image' => 'The image field must be an image.']);

        $this->sendRequest(['image' => $this->image('image.bin')])
            ->onlyHasErrors('image')
            ->assertSessionHasErrors(['image' => 'The image field must be an image.']);

        $this->sendRequest(['image' => $this->image()])
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function image_size_validation()
    {
        Storage::fake('public');

        $this->sendRequest(['image' => $this->image()->size(1025)])
            ->onlyHasErrors('image')
            ->assertSessionHasErrors(['image' => 'The image field must not be greater than 1024 kilobytes.']);

        $this->sendRequest(['image' => $this->image()->size(1024)])
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function image_must_be_at_least_400px_wide()
    {
        Storage::fake('public');

        $this->sendRequest(['image' => $this->image(width: 300, height: 300 * 3 / 5)])
            ->onlyHasErrors('image')
            ->assertSessionHasErrors(['image' => 'The image field has invalid image dimensions.']);

        $this->sendRequest(['image' => $this->image(width: 400, height: 240)])
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function image_must_have_5_3_aspect_ratio()
    {
        Storage::fake('public');

        $this->sendRequest(['image' => $this->image(width: 400, height: 241)])
            ->onlyHasErrors('image')
            ->assertSessionHasErrors(['image' => 'The image field has invalid image dimensions.']);

        $this->sendRequest(['image' => $this->image(width: 400, height: 240)])
            ->assertSessionHasNoErrors();
    }
}
