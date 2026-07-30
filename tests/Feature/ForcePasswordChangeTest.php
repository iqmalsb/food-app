<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForcePasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $org;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org = Organisation::create([
            'name' => 'Pizza Palace',
            'slug' => 'pizza-palace',
            'seats_limit' => 5,
        ]);

        $this->user = User::factory()->create([
            'organisation_id' => $this->org->id,
            'role' => 'staff',
            'must_change_password' => false,
        ]);
    }

    public function test_standard_user_can_access_dashboard()
    {
        $response = $this->actingAs($this->user)->get(route('home'));
        $response->assertStatus(200);
    }

    public function test_forced_user_gets_redirected_to_change_password()
    {
        $this->user->update(['must_change_password' => true]);

        $response = $this->actingAs($this->user)->get(route('home'));
        $response->assertRedirect(route('password.change-form'));
        
        $response2 = $this->actingAs($this->user)->get(route('password.change-form'));
        $response2->assertStatus(200);
        $response2->assertSee('Change Password Required');
    }

    public function test_forced_user_can_reset_password_and_access_dashboard()
    {
        $this->user->update(['must_change_password' => true]);

        $response = $this->actingAs($this->user)->post(route('password.change'), [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertRedirect(route('home'));
        
        $this->user->refresh();
        $this->assertFalse($this->user->must_change_password);
        $this->assertTrue(Hash::check('new-secure-password', $this->user->password));

        // Re-request home to verify access
        $response2 = $this->actingAs($this->user)->get(route('home'));
        $response2->assertStatus(200);
    }

    public function test_superadmin_simulating_user_bypasses_redirect()
    {
        $this->user->update(['must_change_password' => true]);

        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'organisation_id' => null,
        ]);

        // Access home acting as $this->user BUT with the impersonation session key active
        $response = $this->actingAs($this->user)
            ->withSession(['original_superadmin_id' => $superadmin->id])
            ->get(route('home'));

        // Should successfully render without getting redirected!
        $response->assertStatus(200);
    }
}
