<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->sentence(10),
            'status' => TaskStatus::Todo->value,
            'due_at' => null,
            'assigned_to' => null,
            'created_by' => User::factory(),
            'position' => 0,
        ];
    }
}
