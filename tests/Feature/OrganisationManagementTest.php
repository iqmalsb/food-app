<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganisationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;
    protected $orgAdmin;
    protected $organisation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organisation = Organisation::create([
            'name' => 'Pizza House',
            'slug' => 'pizza-house',
            'seats_limit' => 3,
        ]);

        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
            'organisation_id' => null,
        ]);

        $this->orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->organisation->id,
        ]);
    }

    public function test_guests_cannot_access_organisations()
    {
        $response = $this->get(route('organisations.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_org_admins_cannot_access_organisations()
    {
        $response = $this->actingAs($this->orgAdmin)->get(route('organisations.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->orgAdmin)->post(route('organisations.store'), [
            'name' => 'Burger Queen',
            'slug' => 'burger-queen',
            'seats_limit' => 10,
        ]);
        $response->assertStatus(403);
    }

    public function test_superadmin_can_view_organisations_list()
    {
        $response = $this->actingAs($this->superadmin)->get(route('organisations.index'));
        $response->assertStatus(200);
        $response->assertSee('Organisation Management (Superadmin)');
        $response->assertSee($this->organisation->name);
    }

    public function test_superadmin_can_create_organisation()
    {
        $response = $this->actingAs($this->superadmin)->post(route('organisations.store'), [
            'name' => 'Burger Queen',
            'slug' => 'burger-queen',
            'seats_limit' => 10,
        ]);

        $response->assertRedirect(route('organisations.index'));
        $this->assertDatabaseHas('organisations', [
            'slug' => 'burger-queen',
            'seats_limit' => 10,
        ]);
    }

    public function test_superadmin_can_update_organisation()
    {
        $response = $this->actingAs($this->superadmin)->post(route('organisations.update', $this->organisation), [
            'name' => 'Pizza House Updated',
            'slug' => 'pizza-house-updated',
            'seats_limit' => 5,
        ]);

        $response->assertRedirect(route('organisations.index'));
        $this->assertDatabaseHas('organisations', [
            'id' => $this->organisation->id,
            'name' => 'Pizza House Updated',
            'slug' => 'pizza-house-updated',
            'seats_limit' => 5,
        ]);
    }

    public function test_superadmin_can_delete_organisation()
    {
        $response = $this->actingAs($this->superadmin)->get(route('organisations.delete', $this->organisation));
        $response->assertRedirect(route('organisations.index'));
        $this->assertDatabaseMissing('organisations', [
            'id' => $this->organisation->id,
        ]);
    }
}
