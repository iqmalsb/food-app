<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Food;
use App\Models\Category;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminTenantContextTest extends TestCase
{
    use RefreshDatabase;

    protected $org1;
    protected $org2;
    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org1 = Organisation::create([
            'name' => 'Org One',
            'slug' => 'org-one',
            'seats_limit' => 3,
        ]);

        $this->org2 = Organisation::create([
            'name' => 'Org Two',
            'slug' => 'org-two',
            'seats_limit' => 5,
        ]);

        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
            'organisation_id' => null,
        ]);
    }

    public function test_superadmin_can_switch_tenant_context()
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('organisations.switch'), [
                'organisation_id' => $this->org1->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('current_organisation_id', $this->org1->id);

        // Switch back / clear context
        $response = $this->actingAs($this->superadmin)
            ->post(route('organisations.switch'), [
                'organisation_id' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionMissing('current_organisation_id');
    }

    public function test_superadmin_tenant_context_scopes_queries()
    {
        // Set up dummy data using different organisation IDs
        \App\Traits\BelongsToOrganisation::setCurrentOrganisationId($this->org1->id);
        $cat1 = Category::create([
            'name' => 'Pizza',
            'description' => 'Cheesy pizzas',
            'image' => 'pizza.jpg',
        ]);
        $food1 = Food::create([
            'name' => 'Pepperoni Pizza',
            'description' => 'With pepperoni',
            'image' => 'pepperoni.jpg',
            'price' => '12.99',
            'category_id' => $cat1->id,
        ]);
        $table1 = Table::create([
            'table_no' => 'T1',
            'max_pax' => 4,
            'status' => 'available',
        ]);

        \App\Traits\BelongsToOrganisation::setCurrentOrganisationId($this->org2->id);
        $cat2 = Category::create([
            'name' => 'Burger',
            'description' => 'Juicy burgers',
            'image' => 'burger.jpg',
        ]);
        $food2 = Food::create([
            'name' => 'Cheese Burger',
            'description' => 'With cheese',
            'image' => 'cheese.jpg',
            'price' => '8.99',
            'category_id' => $cat2->id,
        ]);
        $table2 = Table::create([
            'table_no' => 'T2',
            'max_pax' => 2,
            'status' => 'available',
        ]);

        // Clear static context to verify session-based behavior
        \App\Traits\BelongsToOrganisation::setCurrentOrganisationId(null);

        // 1. Without context (Global View), superadmin sees everything
        $response = $this->actingAs($this->superadmin)->get(route('food.index'));
        $response->assertSee('Pepperoni Pizza');
        $response->assertSee('Cheese Burger');
        $response->assertSee('Org One');
        $response->assertSee('Org Two');

        $response = $this->actingAs($this->superadmin)->get(route('categories.index'));
        $response->assertSee('Pizza');
        $response->assertSee('Burger');

        $response = $this->actingAs($this->superadmin)->get(route('tables.index'));
        $response->assertSee('T1');
        $response->assertSee('T2');

        // 2. Switch to Org 1
        $this->actingAs($this->superadmin)->post(route('organisations.switch'), [
            'organisation_id' => $this->org1->id,
        ]);

        $response = $this->actingAs($this->superadmin)->get(route('food.index'));
        $response->assertSee('Pepperoni Pizza');
        $response->assertDontSee('Cheese Burger');

        $response = $this->actingAs($this->superadmin)->get(route('categories.index'));
        $response->assertSee('Pizza');
        $response->assertDontSee('Burger');

        $response = $this->actingAs($this->superadmin)->get(route('tables.index'));
        $response->assertSee('T1');
        $response->assertDontSee('T2');

        // 3. Switch to Org 2
        $this->actingAs($this->superadmin)->post(route('organisations.switch'), [
            'organisation_id' => $this->org2->id,
        ]);

        $response = $this->actingAs($this->superadmin)->get(route('food.index'));
        $response->assertDontSee('Pepperoni Pizza');
        $response->assertSee('Cheese Burger');

        $response = $this->actingAs($this->superadmin)->get(route('categories.index'));
        $response->assertDontSee('Pizza');
        $response->assertSee('Burger');

        $response = $this->actingAs($this->superadmin)->get(route('tables.index'));
        $response->assertDontSee('T1');
        $response->assertSee('T2');
    }

    public function test_superadmin_tenant_context_assigns_organisation_id_on_create()
    {
        // 1. Switch to Org 1
        $this->actingAs($this->superadmin)->post(route('organisations.switch'), [
            'organisation_id' => $this->org1->id,
        ]);

        // Create Category under Org 1 context
        $category = Category::create([
            'name' => 'Sushi',
            'description' => 'Sushi category',
            'image' => 'sushi.jpg',
        ]);

        $this->assertEquals($this->org1->id, $category->organisation_id);

        // Create Table under Org 1 context
        $table = Table::create([
            'table_no' => 'T10',
            'max_pax' => 6,
            'status' => 'available',
        ]);

        $this->assertEquals($this->org1->id, $table->organisation_id);
    }

    public function test_superadmin_can_impersonate_user()
    {
        $staffUser = User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org1->id,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->post(route('users.impersonate', $staffUser));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('original_superadmin_id', $this->superadmin->id);
        
        // Assert that we are now logged in as the staff user
        $this->assertEquals($staffUser->id, auth()->id());
    }

    public function test_impersonated_user_can_exit_impersonation()
    {
        $staffUser = User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org1->id,
        ]);

        // Start simulation
        $this->actingAs($this->superadmin)
            ->post(route('users.impersonate', $staffUser));

        // Now verify we can exit impersonation (which should log us back as superadmin)
        $response = $this->actingAs($staffUser)
            ->withSession(['original_superadmin_id' => $this->superadmin->id])
            ->post(route('users.stop-impersonation'));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionMissing('original_superadmin_id');
        $this->assertEquals($this->superadmin->id, auth()->id());
    }

    public function test_non_superadmin_cannot_impersonate()
    {
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organisation_id' => $this->org1->id,
        ]);

        $staffUser = User::factory()->create([
            'role' => 'staff',
            'organisation_id' => $this->org1->id,
        ]);

        $response = $this->actingAs($orgAdmin)
            ->post(route('users.impersonate', $staffUser));

        $response->assertStatus(403);
    }
}
