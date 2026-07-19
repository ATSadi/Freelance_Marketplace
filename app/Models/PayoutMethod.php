<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayoutMethod extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'account_name',
        'bank_name',
        'account_number',
        'routing_number',
        'account_last_four',
        'country',
        'currency',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'account_number' => 'encrypted',
            'routing_number' => 'encrypted',
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function maskedAccount(): string
    {
        return '•••• '.$this->account_last_four;
    }
}
