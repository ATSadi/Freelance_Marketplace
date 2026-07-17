<?php

namespace App\Services;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class EscrowService
{
    /**
     * Hold milestone funds in mock escrow when a milestone is created.
     */
    public function hold(Milestone $milestone): Transaction
    {
        $project = $milestone->project;

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
     * Release escrowed funds to the freelancer after milestone approval.
     */
    public function release(Milestone $milestone): Transaction
    {
        return DB::transaction(function () use ($milestone) {
            $project = $milestone->project;

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
     * Refund escrowed funds when a pending milestone is deleted.
     */
    public function refund(Milestone $milestone): ?Transaction
    {
        $hasHold = Transaction::query()
            ->where('milestone_id', $milestone->id)
            ->where('type', Transaction::TYPE_ESCROW_HOLD)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->exists();

        if (! $hasHold) {
            return null;
        }

        $alreadyReleased = Transaction::query()
            ->where('milestone_id', $milestone->id)
            ->where('type', Transaction::TYPE_ESCROW_RELEASE)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->exists();

        if ($alreadyReleased) {
            return null;
        }

        $project = $milestone->project;

        return Transaction::create([
            'project_id' => $project->id,
            'milestone_id' => $milestone->id,
            'payer_id' => $project->client_id,
            'payee_id' => $project->client_id,
            'amount' => $milestone->amount,
            'type' => Transaction::TYPE_REFUND,
            'status' => Transaction::STATUS_COMPLETED,
            'description' => 'Escrow refunded for deleted milestone: '.$milestone->title,
        ]);
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
}
