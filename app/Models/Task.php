<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $due_at
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'due_at', 'assigned_to', 'created_by', 'position'];

    protected function casts(): array
    {
        return ['status' => TaskStatus::class, 'due_at' => 'datetime'];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOverdue(): bool
    {
        return $this->due_at !== null && $this->due_at->isPast() && $this->status !== TaskStatus::Done;
    }
}
