<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Milestone extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_PAID = 'paid';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'amount',
        'due_date',
        'order_index',
        'status',
        'submission_notes',
        'client_feedback',
        'started_at',
        'submitted_at',
        'approved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'order_index' => 'integer',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * A milestone is considered "completed" once approved or paid.
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PAID], true);
    }

    public function canBeStarted(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function canBeReviewed(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }
}
