<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public const TYPE_ESCROW_HOLD = 'escrow_hold';

    public const TYPE_ESCROW_RELEASE = 'escrow_release';

    public const TYPE_REFUND = 'refund';

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'milestone_id',
        'payer_id',
        'payee_id',
        'amount',
        'type',
        'status',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_ESCROW_HOLD => 'Escrow Hold',
            self::TYPE_ESCROW_RELEASE => 'Payment Released',
            self::TYPE_REFUND => 'Refund',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
