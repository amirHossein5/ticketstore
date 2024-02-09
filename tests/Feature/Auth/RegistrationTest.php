<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function getRegister(): TestResponse
    {
        return $this->get(URL::signedRoute('register'));
    }

    private function postRegister(array $data): TestResponse
    {
        return $this->post(URL::signedRoute('register'), $data);
    }

    /** @test */
    public function get_and_post_registration_routes_require_signed_routes()
    {
        $this->get('/register')->assertStatus(404);

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(404);
    }

    /** @test */
    public function registration_screen_can_be_rendered(): void
    {
        $this->getRegister()->assertStatus(200);
    }

    /** @test */
    public function register_page_contains_proper_form_action()
    {
        $this->get($url = URL::temporarySignedRoute('register', now()->addMinute()))
            ->assertSee('action="'.e($url).'"', false);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->postRegister([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
