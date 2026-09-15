<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Enums\TaskStatus;
use App\Livewire\Admin\TaskBoard;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskBoardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function staff(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Admin->value);

        return $user;
    }

    public function test_customer_cannot_open_the_task_board(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Customer->value);

        $this->actingAs($user)->get(route('admin.tasks'))->assertForbidden();
    }

    public function test_staff_can_create_a_task(): void
    {
        Livewire::actingAs($this->staff())
            ->test(TaskBoard::class)
            ->call('create')
            ->set('title', 'Prepare launch checklist')
            ->call('save')
            ->assertHasNoErrors();

        $task = Task::where('title', 'Prepare launch checklist')->firstOrFail();
        $this->assertSame(TaskStatus::Todo, $task->status);
    }

    public function test_staff_can_move_a_task_between_columns(): void
    {
        $task = Task::factory()->create(['created_by' => $this->staff()->id]);

        Livewire::actingAs($this->staff())
            ->test(TaskBoard::class)
            ->call('moveTo', $task->id, TaskStatus::InProgress->value);

        $this->assertSame(TaskStatus::InProgress, $task->fresh()->status);
    }

    public function test_a_task_past_its_due_date_and_not_done_is_overdue(): void
    {
        $task = Task::factory()->create(['due_at' => now()->subDay(), 'status' => TaskStatus::Todo->value]);
        $doneTask = Task::factory()->create(['due_at' => now()->subDay(), 'status' => TaskStatus::Done->value]);

        $this->assertTrue($task->isOverdue());
        $this->assertFalse($doneTask->isOverdue());
    }
}
