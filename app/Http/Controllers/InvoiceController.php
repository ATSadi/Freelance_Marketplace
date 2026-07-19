<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    /**
     * Show a printable invoice for a paid milestone.
     */
    public function show(Milestone $milestone): View
    {
        abort_unless($milestone->status === Milestone::STATUS_PAID, 404);

        $milestone->load(['project.client.profile', 'project.freelancer.profile']);
        $project = $milestone->project;
        $user = request()->user();

        abort_unless(
            $user->id === $project->client_id
            || $user->id === $project->freelancer_id
            || $user->role === User::ROLE_ADMIN,
            403
        );

        $transaction = Transaction::query()
            ->where('milestone_id', $milestone->id)
            ->where('type', Transaction::TYPE_ESCROW_RELEASE)
            ->latest()
            ->first();

        return view('invoices.show', [
            'milestone' => $milestone,
            'project' => $project,
            'transaction' => $transaction,
            'invoiceNumber' => 'WV-'.str_pad((string) $milestone->id, 5, '0', STR_PAD_LEFT),
        ]);
    }

    /**
     * Download invoice as a simple HTML file (print-ready / save as PDF).
     */
    public function download(Milestone $milestone): StreamedResponse
    {
        abort_unless($milestone->status === Milestone::STATUS_PAID, 404);

        $milestone->load(['project.client', 'project.freelancer']);
        $project = $milestone->project;
        $user = request()->user();

        abort_unless(
            $user->id === $project->client_id
            || $user->id === $project->freelancer_id
            || $user->role === User::ROLE_ADMIN,
            403
        );

        $invoiceNumber = 'WV-'.str_pad((string) $milestone->id, 5, '0', STR_PAD_LEFT);
        $html = view('invoices.pdf', compact('milestone', 'project', 'invoiceNumber'))->render();

        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, $invoiceNumber.'.html', [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
