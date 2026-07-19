<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_CLIENT = 'client';

    public const ROLE_FREELANCER = 'freelancer';

    public const ROLE_ADMIN = 'admin';

    /**
     * Get the dashboard route for this user's role.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_CLIENT => route('client.dashboard', absolute: false),
            self::ROLE_FREELANCER => route('freelancer.dashboard', absolute: false),
            self::ROLE_ADMIN => route('admin.dashboard', absolute: false),
            default => route('dashboard', absolute: false),
        };
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'freelancer_id');
    }

    /**
     * Projects this freelancer has been assigned to (accepted proposal).
     */
    public function assignedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'freelancer_id');
    }

    public function paidTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'payee_id');
    }

    public function fundedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'payer_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function savedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'saved_projects')->withTimestamps();
    }

    public function receivedReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function writtenReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function payoutMethods(): HasMany
    {
        return $this->hasMany(PayoutMethod::class);
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function averageRating(): ?float
    {
        $average = $this->receivedReviews()->avg('rating');

        return $average === null ? null : round((float) $average, 1);
    }

    /**
     * Whether the user has filled in all required profile fields for their role.
     */
    public function isProfileComplete(): bool
    {
        if ($this->role === self::ROLE_ADMIN) {
            return true;
        }

        $profile = $this->profile;

        if (! $profile) {
            return false;
        }

        return match ($this->role) {
            self::ROLE_CLIENT => filled($profile->bio)
                && filled($profile->company_name)
                && filled($profile->phone),
            self::ROLE_FREELANCER => filled($profile->bio)
                && filled($profile->skills)
                && filled($profile->hourly_rate)
                && filled($profile->phone),
            default => true,
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
