<?php

namespace Tests\Feature;

use App\Mail\ClientSignedUp;
use App\Mail\NewClientNotif;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_sends_emails_after_successful_registration()
    {
        Mail::fake();

        $response = $this->post(route('register.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '1234567890',
        ]);

        $response->assertRedirect();

        Mail::assertSent(ClientSignedUp::class, function ($mail) {
            return $mail->hasTo('john.doe@example.com');
        });

        Mail::assertSent(NewClientNotif::class, function ($mail) {
            return $mail->hasTo('admin@yourdomain.com');
        });
    }
}