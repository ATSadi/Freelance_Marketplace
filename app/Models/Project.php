<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'freelancer_id',
        'title',
        'description',
        'budget_min',
        'budget_max',
        'deadline',
        'status',
        'category',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'deadline' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * The freelancer assigned to this project after a proposal is accepted.
     */
    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * The accepted proposal for this project, if any.
     */
    public function acceptedProposal(): HasMany
    {
        return $this->hasMany(Proposal::class)->where('status', Proposal::STATUS_ACCEPTED);
    }

    /**
     * Human-readable budget range.
     */
    public function budgetRange(): string
    {
        return '$'.number_format((float) $this->budget_min, 2)
            .' - $'.number_format((float) $this->budget_max, 2);
    }
}
