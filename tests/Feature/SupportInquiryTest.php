<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use App\Mail\SupportInquiryMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupportInquiryTest extends TestCase
{
    use RefreshDatabase;

    protected $organisation;
    protected $orgAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organisation = Organisation::create([
            'name' => 'Pizza Palace',
            'slug' => 'pizza-palace',
            'seats_limit' => 1,
        ]);

        $this->orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->organisation->id,
            'must_change_password' => false,
        ]);
    }

    public function test_org_admin_blocked_from_creating_users_when_seats_limit_reached()
    {
        // Seat limit is 1, and we already have 1 user (the orgAdmin) in the database.
        // Therefore, seats remaining = 0.
        
        $response = $this->actingAs($this->orgAdmin)->get(route('users.create'));
        
        // Assert redirect back to user index with seats warning message
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('alert-message', 'Your organisation has reached its seats limit (1 seats). You cannot create more users.');
    }

    public function test_org_admin_can_access_user_creation_when_seats_are_available()
    {
        // Increase seat limit to 2, leaving 1 seat vacant.
        $this->organisation->update(['seats_limit' => 2]);

        $response = $this->actingAs($this->orgAdmin)->get(route('users.create'));
        
        $response->assertStatus(200);
    }

    public function test_submitting_support_inquiry_sends_email_notification_to_superadmins()
    {
        Mail::fake();

        // Create a superadmin user
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'organisation_id' => null,
        ]);

        $response = $this->actingAs($this->orgAdmin)->post(route('support.inquiry'), [
            'inquiry_type' => 'upgrade_package',
            'subject' => 'Requesting seat limit upgrade',
            'message' => 'Please increase our limit to 10 seats.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('alert-message', 'Your inquiry has been submitted successfully to support!');

        // Assert that the email was dispatched to the superadmin
        Mail::assertSent(SupportInquiryMail::class, function ($mail) use ($superadmin) {
            return $mail->hasTo($superadmin->email) &&
                   $mail->inquiryType === 'upgrade_package' &&
                   $mail->subject === 'Requesting seat limit upgrade' &&
                   $mail->messageContent === 'Please increase our limit to 10 seats.' &&
                   $mail->user->id === $this->orgAdmin->id;
        });
    }
}
