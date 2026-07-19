<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $client_id
 * @property int|null $freelancer_id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property string $category
 * @property Carbon $deadline
 * @property Carbon $created_at
 */
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

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('order_index');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function savedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_projects')->withTimestamps();
    }

    /**
     * The accepted proposal for this project, if any.
     */
    public function acceptedProposal(): HasMany
    {
        return $this->hasMany(Proposal::class)->where('status', Proposal::STATUS_ACCEPTED);
    }

    /**
     * The single accepted proposal record, or null.
     */
    public function acceptedProposalRecord(): ?Proposal
    {
        return $this->proposals()
            ->where('status', Proposal::STATUS_ACCEPTED)
            ->first();
    }

    /**
     * The agreed budget for this project (the accepted proposal amount).
     */
    public function agreedAmount(): float
    {
        return (float) ($this->acceptedProposalRecord()?->proposed_amount ?? 0);
    }

    /**
     * Sum of all milestone amounts for this project.
     */
    public function milestonesTotal(): float
    {
        return (float) $this->milestones()->sum('amount');
    }

    /**
     * Remaining budget that can still be allocated to milestones.
     */
    public function remainingBudget(): float
    {
        return $this->agreedAmount() - $this->milestonesTotal();
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
