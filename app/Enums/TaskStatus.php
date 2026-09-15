<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';

    public function label(): string
    {
        return __('admin.tasks.status.'.$this->value);
    }
}
