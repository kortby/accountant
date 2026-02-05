<?php

use App\Models\ClientProfile;
use App\Models\User;

test('spouse information is saved when married filing jointly', function () {
    $user = User::factory()->create(['role' => 'client']);

    $this->actingAs($user);

    $response = $this->post(route('tax-information.store'), [
        'tax_year' => 2025,
        'ssn' => '123-45-6789',
        'date_of_birth' => '1980-01-01',
        'occupation' => 'Engineer',
        'marital_status' => 'married_filing_jointly',
        'address' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'zip_code' => '10001',
        'spouse_first_name' => 'Jane',
        'spouse_middle_name' => 'Marie',
        'spouse_last_name' => 'Doe',
        'spouse_social_security_number' => '987-65-4321',
        'spouse_date_of_birth' => '1982-05-15',
        'spouse_occupation' => 'Teacher',
        'income_types' => ['w2'],
        'dependents' => [],
        'documents' => [],
    ]);

    $response->assertRedirect(route('dashboard'));

    $profile = ClientProfile::where('user_id', $user->id)->first();

    expect($profile)->not->toBeNull()
        ->and($profile->marital_status)->toBe('married_filing_jointly')
        ->and($profile->spouse_first_name)->toBe('Jane')
        ->and($profile->spouse_middle_name)->toBe('Marie')
        ->and($profile->spouse_last_name)->toBe('Doe')
        ->and($profile->spouse_social_security_number)->toBe('987-65-4321')
        ->and($profile->spouse_date_of_birth->format('Y-m-d'))->toBe('1982-05-15')
        ->and($profile->spouse_occupation)->toBe('Teacher');
});

test('spouse information is required when married filing jointly', function () {
    $user = User::factory()->create(['role' => 'client']);

    $this->actingAs($user);

    $response = $this->post(route('tax-information.store'), [
        'tax_year' => 2025,
        'ssn' => '123-45-6789',
        'date_of_birth' => '1980-01-01',
        'occupation' => 'Engineer',
        'marital_status' => 'married_filing_jointly',
        'address' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'zip_code' => '10001',
        'income_types' => ['w2'],
        'dependents' => [],
        'documents' => [],
    ]);

    $response->assertSessionHasErrors([
        'spouse_first_name',
        'spouse_last_name',
        'spouse_social_security_number',
        'spouse_date_of_birth',
        'spouse_occupation',
    ]);
});

test('spouse information is not required when not married filing jointly', function () {
    $user = User::factory()->create(['role' => 'client']);

    $this->actingAs($user);

    $response = $this->post(route('tax-information.store'), [
        'tax_year' => 2025,
        'ssn' => '123-45-6789',
        'date_of_birth' => '1980-01-01',
        'occupation' => 'Engineer',
        'marital_status' => 'single',
        'address' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'zip_code' => '10001',
        'income_types' => ['w2'],
        'dependents' => [],
        'documents' => [],
    ]);

    $response->assertRedirect(route('dashboard'));

    $profile = ClientProfile::where('user_id', $user->id)->first();

    expect($profile)->not->toBeNull()
        ->and($profile->marital_status)->toBe('single')
        ->and($profile->spouse_first_name)->toBeNull()
        ->and($profile->spouse_last_name)->toBeNull();
});
