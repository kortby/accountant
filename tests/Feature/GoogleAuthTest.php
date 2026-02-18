<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

it('redirects to Google OAuth', function () {
    Socialite::fake('google');

    $response = $this->get('/auth/google/redirect');

    $response->assertRedirect();
});

it('creates a new user from Google callback', function () {
    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-123',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]));

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('users', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'google_id' => 'google-123',
    ]);

    $this->assertAuthenticated();
});

it('links Google account to existing user by email', function () {
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'google_id' => null,
    ]);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-456',
        'name' => 'Existing User',
        'email' => 'existing@example.com',
    ]));

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect('/dashboard');

    expect($user->fresh()->google_id)->toBe('google-456');

    $this->assertAuthenticatedAs($user);
});

it('logs in existing Google-linked user', function () {
    $user = User::factory()->create([
        'google_id' => 'google-789',
    ]);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-789',
        'name' => $user->name,
        'email' => $user->email,
    ]));

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);

    // Should not create a new user
    expect(User::count())->toBe(1);
});

it('handles single-name Google users', function () {
    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-mono',
        'name' => 'Madonna',
        'email' => 'madonna@example.com',
    ]));

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('users', [
        'first_name' => 'Madonna',
        'last_name' => '',
        'email' => 'madonna@example.com',
        'google_id' => 'google-mono',
    ]);
});

it('marks email as verified for Google users', function () {
    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-verified',
        'name' => 'Verified User',
        'email' => 'verified@example.com',
    ]));

    $this->get('/auth/google/callback');

    $user = User::where('email', 'verified@example.com')->first();

    expect($user->email_verified_at)->not->toBeNull();
});
