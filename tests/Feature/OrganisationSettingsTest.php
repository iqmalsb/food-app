<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrganisationSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $org;
    protected $orgAdmin;
    protected $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org = Organisation::create([
            'name' => 'Tasty Bytes',
            'slug' => 'tasty-bytes',
            'seats_limit' => 5,
        ]);

        $this->orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->org->id,
        ]);

        $this->staff = User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org->id,
        ]);
    }

    public function test_guests_cannot_access_settings()
    {
        $response = $this->get(route('organisation.settings'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('organisation.update-settings'), [
            'theme_color' => 'emerald',
            'theme_mode' => 'light',
        ]);
        $response->assertRedirect(route('login'));
    }

    public function test_staff_cannot_access_settings()
    {
        $response = $this->actingAs($this->staff)->get(route('organisation.settings'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->staff)->post(route('organisation.update-settings'), [
            'theme_color' => 'emerald',
            'theme_mode' => 'light',
        ]);
        $response->assertStatus(403);
    }

    public function test_org_admin_can_view_settings()
    {
        $response = $this->actingAs($this->orgAdmin)->get(route('organisation.settings'));
        $response->assertStatus(200);
        $response->assertSee('Organisation Settings');
        $response->assertSee('Default Theme Color');
    }

    public function test_org_admin_can_update_theme_settings()
    {
        $response = $this->actingAs($this->orgAdmin)->post(route('organisation.update-settings'), [
            'theme_color' => 'emerald',
            'theme_mode' => 'light',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('organisations', [
            'id' => $this->org->id,
            'theme_color' => 'emerald',
            'theme_mode' => 'light',
        ]);
    }

    public function test_org_admin_can_upload_banner_image()
    {
        Storage::fake('public');

        $banner = UploadedFile::fake()->image('my-banner.jpg', 1200, 300);

        $response = $this->actingAs($this->orgAdmin)->post(route('organisation.update-settings'), [
            'theme_color' => 'blue',
            'theme_mode' => 'dark',
            'banner_image' => $banner,
        ]);

        $response->assertRedirect();

        // Refresh org from DB
        $this->org->refresh();

        $this->assertNotNull($this->org->banner_image);
        Storage::disk('public')->assertExists($this->org->banner_image);
    }

    public function test_superadmin_can_view_settings_without_banner_elements()
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'organisation_id' => null,
        ]);

        $response = $this->actingAs($superadmin)->get(route('organisation.settings'));
        $response->assertStatus(200);
        $response->assertSee('Settings');
        $response->assertDontSee('Current Organisation Banner');
        $response->assertDontSee('Upload New Banner');
    }

    public function test_superadmin_can_update_own_theme_settings()
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'organisation_id' => null,
        ]);

        $response = $this->actingAs($superadmin)->post(route('organisation.update-settings'), [
            'theme_color' => 'rose',
            'theme_mode' => 'light',
        ]);

        $response->assertRedirect();
        
        $superadmin->refresh();
        $this->assertEquals('rose', $superadmin->theme_color);
        $this->assertEquals('light', $superadmin->theme_mode);
    }
}
