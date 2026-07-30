<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Food;
use App\Models\Category;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $org1;
    protected $org2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org1 = Organisation::create([
            'name' => 'Org 1',
            'slug' => 'org-1',
            'seats_limit' => 2,
        ]);

        $this->org2 = Organisation::create([
            'name' => 'Org 2',
            'slug' => 'org-2',
            'seats_limit' => 5,
        ]);
    }

    public function test_guests_cannot_access_user_management()
    {
        $response = $this->get(route('users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_staff_cannot_access_user_management()
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org1->id,
        ]);

        $response = $this->actingAs($staff)->get(route('users.index'));
        $response->assertStatus(403);
    }

    public function test_org_admin_can_view_users_list_for_their_organisation()
    {
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->org1->id,
        ]);

        // A user belonging to org2
        $otherUser = User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org2->id,
        ]);

        $response = $this->actingAs($orgAdmin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('User Management (Organisation Admin)');
        $response->assertSee($orgAdmin->name);
        $response->assertDontSee($otherUser->name); // Org isolation check
    }

    public function test_superadmin_can_view_all_users()
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'organisation_id' => null,
        ]);

        $user1 = User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org1->id,
        ]);

        $user2 = User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org2->id,
        ]);

        $response = $this->actingAs($superadmin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('User Management (Superadmin)');
        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
    }

    public function test_org_admin_can_create_user_within_seat_limit()
    {
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->org1->id,
        ]);

        $response = $this->actingAs($orgAdmin)->post(route('users.store'), [
            'name' => 'New Staff',
            'email' => 'staff@org1.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'staff',
            'position' => 'Waiter',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'staff@org1.com',
            'organisation_id' => $this->org1->id,
            'role' => 'staff',
        ]);
    }

    public function test_org_admin_cannot_exceed_seat_limit()
    {
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->org1->id, // limit is 2
        ]);

        // Creating second user (reaches limit)
        User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org1->id,
        ]);

        // Attempting to create third user (exceeds limit)
        $response = $this->actingAs($orgAdmin)->post(route('users.store'), [
            'name' => 'Third Staff',
            'email' => 'third@org1.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'staff',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', [
            'email' => 'third@org1.com',
        ]);
    }

    public function test_org_admin_cannot_view_or_edit_users_from_other_organisation()
    {
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->org1->id,
        ]);

        $otherUser = User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org2->id,
        ]);

        $response = $this->actingAs($orgAdmin)->get(route('users.show', $otherUser));
        $response->assertStatus(404);
    }

    public function test_org_admin_can_update_user_within_org()
    {
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->org1->id,
        ]);

        $staff = User::factory()->create([
            'name' => 'Old Name',
            'role' => 'staff',
            'organisation_id' => $this->org1->id,
        ]);

        $response = $this->actingAs($orgAdmin)->post(route('users.update', $staff), [
            'name' => 'New Name',
            'email' => $staff->email,
            'role' => 'manager',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'name' => 'New Name',
            'role' => 'manager',
        ]);
    }

    public function test_org_admin_cannot_delete_user_from_other_org()
    {
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->org1->id,
        ]);

        $otherUser = User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org2->id,
        ]);

        $response = $this->actingAs($orgAdmin)->get(route('users.delete', $otherUser));
        $response->assertStatus(404);
    }

    public function test_multi_tenant_data_isolation()
    {
        // Set context to Org 1
        \App\Traits\BelongsToOrganisation::setCurrentOrganisationId($this->org1->id);

        $cat1 = Category::create([
            'name' => 'Pizza',
            'description' => 'Pizza category',
            'image' => 'pizza.jpg',
        ]);

        $food1 = Food::create([
            'name' => 'Margherita',
            'description' => 'Tomato and cheese',
            'price' => '12.99',
            'image' => 'pizza.jpg',
            'category_id' => $cat1->id,
        ]);

        $table1 = Table::create([
            'table_no' => 'T1',
            'max_pax' => 4,
            'status' => 'available',
        ]);

        // Set context to Org 2
        \App\Traits\BelongsToOrganisation::setCurrentOrganisationId($this->org2->id);

        $cat2 = Category::create([
            'name' => 'Burgers',
            'description' => 'Burger category',
            'image' => 'burger.jpg',
        ]);

        // Check that querying Category, Food, and Table only shows Org 2's data
        $this->assertCount(1, Category::all());
        $this->assertEquals('Burgers', Category::first()->name);

        $this->assertCount(0, Food::all()); // Margherita was created in Org 1

        $this->assertCount(0, Table::all()); // Table 1 was created in Org 1
    }

    public function test_user_list_livewire_component_renders_and_filters_by_search()
    {
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->org1->id,
        ]);

        $user1 = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'organisation_id' => $this->org1->id,
        ]);

        $user2 = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'organisation_id' => $this->org1->id,
        ]);

        \Livewire\Livewire::actingAs($orgAdmin)
            ->test(\App\Livewire\UserList::class)
            ->assertSee('John Doe')
            ->assertSee('Jane Smith')
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith')
            ->call('deleteUser', $user2->id)
            ->assertDispatched('toast');

        $this->assertSoftDeleted('users', ['id' => $user2->id]);
    }
}
