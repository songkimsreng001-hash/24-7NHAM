<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function mockGoogleUser(string $id, string $email, string $name, ?string $avatar = null): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn($id);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getName')->andReturn($name);
        $socialiteUser->shouldReceive('getNickname')->andReturn(null);
        $socialiteUser->shouldReceive('getAvatar')->andReturn($avatar);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('setHttpClient')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_user_is_redirected_to_google_oauth_url(): void
    {
        $response = $this->get(route('auth.google.redirect'));

        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
        $this->assertStringContainsString('client_id=', $response->headers->get('Location'));
    }

    public function test_google_callback_creates_and_logs_in_new_customer(): void
    {
        $this->mockGoogleUser('google-uid-1001', 'newuser@example.com', 'Google Customer', 'https://example.com/photo.jpg');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('google-uid-1001', $user->google_id);
        $this->assertEquals('Google Customer', $user->name);
        $this->assertEquals('customer', $user->role);
        $this->assertEquals('https://example.com/photo.jpg', $user->avatar);
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('active', $user->status);
    }

    public function test_google_callback_links_existing_user_without_google_id(): void
    {
        $existing = User::factory()->create([
            'email' => 'existing@example.com',
            'name' => 'Existing User',
            'google_id' => null,
            'role' => 'customer',
        ]);

        $this->mockGoogleUser('google-uid-2002', 'existing@example.com', 'Existing User Updated', 'https://example.com/new_avatar.jpg');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($existing);

        $existing->refresh();
        $this->assertEquals('google-uid-2002', $existing->google_id);
        $this->assertEquals('https://example.com/new_avatar.jpg', $existing->avatar);
    }

    public function test_google_callback_with_admin_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_google@example.com',
            'name' => 'Admin User',
            'role' => 'admin',
            'google_id' => null,
        ]);

        $this->mockGoogleUser('google-uid-admin', 'admin_google@example.com', 'Admin User');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_google_callback_on_api_route_works(): void
    {
        $this->mockGoogleUser('google-uid-api', 'api_callback@example.com', 'API Callback User');

        $response = $this->get('/api/auth/google/callback');

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = User::where('email', 'api_callback@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('google-uid-api', $user->google_id);
    }

    public function test_google_callback_handles_cancellation_gracefully(): void
    {
        $response = $this->get('/auth/callback?error=access_denied&error_description=User+denied+access');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_google_callback_blocks_suspended_user(): void
    {
        $suspended = User::factory()->create([
            'email' => 'suspended@example.com',
            'name' => 'Suspended Person',
            'status' => 'suspended',
        ]);

        $this->mockGoogleUser('google-uid-suspended', 'suspended@example.com', 'Suspended Person');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
