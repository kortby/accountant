<?php

use App\Jobs\ProcessTaxDocuments;
use App\Models\TaxReturn;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('accountant can view ai settings page', function () {
    $accountant = User::factory()->create(['role' => 'accountant']);

    $this->actingAs($accountant);

    $response = $this->get(route('ai.edit'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/Ai')
            ->has('aiEnabled')
        );
});

test('client can view ai settings page', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client);

    $response = $this->get(route('ai.edit'));

    $response->assertOk();
});

test('accountant can disable ai processing', function () {
    $accountant = User::factory()->create(['role' => 'accountant', 'ai_enabled' => true]);

    $this->actingAs($accountant);

    $response = $this->patch(route('ai.update'), [
        'ai_enabled' => false,
    ]);

    $response->assertRedirect();

    $accountant->refresh();
    expect($accountant->ai_enabled)->toBeFalse();
});

test('accountant can enable ai processing', function () {
    $accountant = User::factory()->create(['role' => 'accountant', 'ai_enabled' => false]);

    $this->actingAs($accountant);

    $response = $this->patch(route('ai.update'), [
        'ai_enabled' => true,
    ]);

    $response->assertRedirect();

    $accountant->refresh();
    expect($accountant->ai_enabled)->toBeTrue();
});

test('ai job is not dispatched when ai is disabled', function () {
    Queue::fake();
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'client', 'ai_enabled' => false]);

    $this->actingAs($user);

    $response = $this->post(route('tax-information.store'), [
        'tax_year' => 2025,
        'ssn' => '123-45-6789',
        'date_of_birth' => '1990-01-01',
        'occupation' => 'Developer',
        'marital_status' => 'single',
        'address' => '456 Oak Ave',
        'city' => 'Austin',
        'state' => 'TX',
        'zip_code' => '73301',
        'income_types' => ['w2'],
        'dependents' => [],
        'documents' => [
            UploadedFile::fake()->create('w2.pdf', 100, 'application/pdf'),
        ],
    ]);

    $response->assertRedirect(route('dashboard'));

    $taxReturn = TaxReturn::where('user_id', $user->id)->first();
    expect($taxReturn)->not->toBeNull()
        ->and($taxReturn->ai_processing_status)->not->toBe('pending');

    Queue::assertNotPushed(ProcessTaxDocuments::class);
});

test('ai job is dispatched when ai is enabled', function () {
    Queue::fake();
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'client', 'ai_enabled' => true]);

    $this->actingAs($user);

    $response = $this->post(route('tax-information.store'), [
        'tax_year' => 2025,
        'ssn' => '123-45-6789',
        'date_of_birth' => '1990-01-01',
        'occupation' => 'Developer',
        'marital_status' => 'single',
        'address' => '456 Oak Ave',
        'city' => 'Austin',
        'state' => 'TX',
        'zip_code' => '73301',
        'income_types' => ['w2'],
        'dependents' => [],
        'documents' => [
            UploadedFile::fake()->create('w2.pdf', 100, 'application/pdf'),
        ],
    ]);

    $response->assertRedirect(route('dashboard'));

    Queue::assertPushed(ProcessTaxDocuments::class);
});

test('ai_enabled validation requires boolean', function () {
    $accountant = User::factory()->create(['role' => 'accountant']);

    $this->actingAs($accountant);

    $response = $this->patch(route('ai.update'), [
        'ai_enabled' => 'not-a-boolean',
    ]);

    $response->assertSessionHasErrors('ai_enabled');
});
