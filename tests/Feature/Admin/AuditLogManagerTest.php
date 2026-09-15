<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_non_admins_cannot_view_the_audit_log(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Support->value);

        $this->actingAs($user)->get(route('admin.audit-log'))->assertForbidden();
    }

    public function test_admins_can_view_the_audit_log(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Admin->value);

        $this->actingAs($user)->get(route('admin.audit-log'))->assertOk();
    }

    public function test_creating_a_category_writes_an_audit_log_entry(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Admin->value);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Ebook', 'slug' => 'ebook']);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'event' => 'created',
            'auditable_type' => Category::class,
            'auditable_id' => $category->id,
        ]);
    }

    public function test_updating_a_category_records_before_and_after_changes(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Admin->value);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Ebook', 'slug' => 'ebook']);
        $category->update(['name' => 'Workbook']);

        $log = AuditLog::where('event', 'updated')
            ->where('auditable_id', $category->id)
            ->firstOrFail();

        $this->assertSame('Ebook', $log->changes['before']['name']);
        $this->assertSame('Workbook', $log->changes['after']['name']);
    }

    public function test_deleting_a_category_writes_an_audit_log_entry(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Admin->value);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Ebook', 'slug' => 'ebook']);
        $category->delete();

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'deleted',
            'auditable_type' => Category::class,
            'auditable_id' => $category->id,
        ]);
    }
}
