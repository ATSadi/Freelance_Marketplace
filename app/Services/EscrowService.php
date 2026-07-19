<?php

namespace App\Services;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class EscrowService
{
    /**
     * Hold milestone funds in mock escrow when a milestone is created or funded.
     * Idempotent: returns the existing hold when one already exists.
     */
    public function hold(Milestone $milestone): Transaction
    {
        $existing = Transaction::query()
            ->where('milestone_id', $milestone->id)
            ->where('type', Transaction::TYPE_ESCROW_HOLD)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->first();

        if ($existing) {
            return $existing;
        }

        $project = $milestone->project()->firstOrFail();

        return Transaction::create([
            'project_id' => $project->id,
            'milestone_id' => $milestone->id,
            'payer_id' => $project->client_id,
            'payee_id' => $project->freelancer_id,
            'amount' => $milestone->amount,
            'type' => Transaction::TYPE_ESCROW_HOLD,
            'status' => Transaction::STATUS_COMPLETED,
            'description' => 'Funds held in escrow for milestone: '.$milestone->title,
        ]);
    }

    /**
     * Keep the escrow hold amount aligned after a pending milestone is edited.
     */
    public function syncHoldAmount(Milestone $milestone): void
    {
        if ($this->hasReleasedOrRefunded($milestone)) {
            return;
        }

        Transaction::query()
            ->where('milestone_id', $milestone->id)
            ->where('type', Transaction::TYPE_ESCROW_HOLD)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->update([
                'amount' => $milestone->amount,
                'description' => 'Funds held in escrow for milestone: '.$milestone->title,
            ]);
    }

    /**
     * Release escrowed funds to the freelancer after milestone approval.
     * Idempotent: returns the existing release when one already exists.
     */
    public function release(Milestone $milestone): Transaction
    {
        return DB::transaction(function () use ($milestone) {
            $existing = Transaction::query()
                ->where('milestone_id', $milestone->id)
                ->where('type', Transaction::TYPE_ESCROW_RELEASE)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                if ($milestone->status !== Milestone::STATUS_PAID) {
                    $milestone->update(['status' => Milestone::STATUS_PAID]);
                }

                return $existing;
            }

            $this->hold($milestone);
            $project = $milestone->project()->firstOrFail();

            $transaction = Transaction::create([
                'project_id' => $project->id,
                'milestone_id' => $milestone->id,
                'payer_id' => $project->client_id,
                'payee_id' => $project->freelancer_id,
                'amount' => $milestone->amount,
                'type' => Transaction::TYPE_ESCROW_RELEASE,
                'status' => Transaction::STATUS_COMPLETED,
                'description' => 'Escrow released for milestone: '.$milestone->title,
            ]);

            $milestone->update(['status' => Milestone::STATUS_PAID]);

            return $transaction;
        });
    }

    /**
     * Refund escrowed funds for a milestone that has not been released.
     */
    public function refund(Milestone $milestone, string $reason = 'Escrow refunded'): ?Transaction
    {
        return DB::transaction(function () use ($milestone, $reason) {
            $hasHold = Transaction::query()
                ->where('milestone_id', $milestone->id)
                ->where('type', Transaction::TYPE_ESCROW_HOLD)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->lockForUpdate()
                ->exists();

            if (! $hasHold || $this->hasReleasedOrRefunded($milestone)) {
                return null;
            }

            $project = $milestone->project()->firstOrFail();

            return Transaction::create([
                'project_id' => $project->id,
                'milestone_id' => $milestone->id,
                'payer_id' => $project->client_id,
                'payee_id' => $project->client_id,
                'amount' => $milestone->amount,
                'type' => Transaction::TYPE_REFUND,
                'status' => Transaction::STATUS_COMPLETED,
                'description' => $reason.': '.$milestone->title,
            ]);
        });
    }

    /**
     * Refund every unreleased hold on a cancelled project.
     */
    public function refundOpenHolds(Project $project): int
    {
        $count = 0;

        $project->loadMissing('milestones');

        foreach ($project->milestones as $milestone) {
            if ($this->refund($milestone, 'Escrow refunded after project cancellation')) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Total still held in escrow for a project (holds minus releases and refunds).
     */
    public function heldBalance(Project $project): float
    {
        $holds = (float) $project->transactions()
            ->where('type', Transaction::TYPE_ESCROW_HOLD)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');

        $released = (float) $project->transactions()
            ->whereIn('type', [Transaction::TYPE_ESCROW_RELEASE, Transaction::TYPE_REFUND])
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');

        return max(0, $holds - $released);
    }

    private function hasReleasedOrRefunded(Milestone $milestone): bool
    {
        return Transaction::query()
            ->where('milestone_id', $milestone->id)
            ->whereIn('type', [Transaction::TYPE_ESCROW_RELEASE, Transaction::TYPE_REFUND])
            ->where('status', Transaction::STATUS_COMPLETED)
            ->exists();
    }
}
