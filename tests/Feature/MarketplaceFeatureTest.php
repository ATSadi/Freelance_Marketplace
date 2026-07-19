<?php

namespace Tests\Feature;

use App\Models\Dispute;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_participants_can_chat_but_outsiders_cannot(): void
    {
        [$client, $freelancer, $project] = $this->projectParticipants();
        $outsider = $this->user(User::ROLE_FREELANCER, 'outsider@example.test');

        $this->actingAs($client)
            ->post(route('messages.store', $project), ['body' => 'Can you share an update?'])
            ->assertRedirect(route('messages.show', $project));

        $this->assertDatabaseHas('messages', [
            'project_id' => $project->id,
            'sender_id' => $client->id,
            'body' => 'Can you share an update?',
        ]);

        $this->actingAs($outsider)->get(route('messages.show', $project))->assertForbidden();
    }

    public function test_freelancer_can_save_and_remove_a_project(): void
    {
        $client = $this->user(User::ROLE_CLIENT, 'client@example.test');
        $freelancer = $this->user(User::ROLE_FREELANCER, 'freelancer@example.test');
        $project = $this->project($client);

        $this->actingAs($freelancer)->post(route('saved-projects.toggle', $project))->assertRedirect();
        $this->assertDatabaseHas('saved_projects', ['user_id' => $freelancer->id, 'project_id' => $project->id]);

        $this->actingAs($freelancer)->post(route('saved-projects.toggle', $project))->assertRedirect();
        $this->assertDatabaseMissing('saved_projects', ['user_id' => $freelancer->id, 'project_id' => $project->id]);
    }

    public function test_completed_project_participants_can_review_once(): void
    {
        [$client, $freelancer, $project] = $this->projectParticipants(Project::STATUS_COMPLETED);

        $this->actingAs($client)->post(route('reviews.store', $project), [
            'rating' => 5,
            'comment' => 'Excellent work and communication.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'project_id' => $project->id,
            'reviewer_id' => $client->id,
            'reviewee_id' => $freelancer->id,
            'rating' => 5,
        ]);

        $this->actingAs($client)->post(route('reviews.store', $project), [
            'rating' => 4,
            'comment' => 'Updated review after delivery.',
        ])->assertRedirect();

        $this->assertSame(1, Review::where('project_id', $project->id)->where('reviewer_id', $client->id)->count());
    }

    public function test_admin_can_suspend_a_user_and_suspended_user_cannot_login(): void
    {
        $admin = $this->user(User::ROLE_ADMIN, 'admin@example.test');
        $client = $this->user(User::ROLE_CLIENT, 'client@example.test');

        $this->actingAs($admin)->patch(route('admin.users.toggle', $client))->assertRedirect();
        $this->assertFalse($client->fresh()->is_active);

        auth()->logout();
        $this->post('/login', ['email' => $client->email, 'password' => 'password']);
        $this->assertGuest();
    }

    public function test_stripe_checkout_fails_gracefully_without_keys(): void
    {
        [$client, , $project] = $this->projectParticipants();
        $milestone = $project->milestones()->create([
            'title' => 'First delivery',
            'description' => 'A funded milestone',
            'amount' => 500,
            'due_date' => now()->addWeek(),
            'order_index' => 1,
            'status' => 'pending',
        ]);

        config(['services.stripe.secret' => null]);

        $this->actingAs($client)
            ->post(route('stripe.checkout', $milestone))
            ->assertSessionHasErrors('stripe');
    }

    public function test_only_order_participants_can_open_order_details(): void
    {
        [$client, $freelancer, $project] = $this->projectParticipants();
        $outsider = $this->user(User::ROLE_FREELANCER, 'order-outsider@example.test');

        $this->actingAs($client)->get(route('orders.show', $project))->assertOk();
        $this->actingAs($freelancer)->get(route('orders.show', $project))->assertOk();
        $this->actingAs($outsider)->get(route('orders.show', $project))->assertForbidden();
    }

    public function test_freelancer_can_request_available_earnings_and_admin_can_approve(): void
    {
        [$client, $freelancer, $project] = $this->projectParticipants();
        $admin = $this->user(User::ROLE_ADMIN, 'withdrawal-admin@example.test');

        Transaction::create([
            'project_id' => $project->id,
            'payer_id' => $client->id,
            'payee_id' => $freelancer->id,
            'amount' => 750,
            'type' => Transaction::TYPE_ESCROW_RELEASE,
            'status' => Transaction::STATUS_COMPLETED,
            'description' => 'Released test earnings',
        ]);

        $this->actingAs($freelancer)->post(route('wallet.methods.store'), [
            'type' => 'bank',
            'account_name' => 'Demo Freelancer',
            'bank_name' => 'Demo Bank',
            'account_number' => '1234567890',
            'routing_number' => '110000000',
            'country' => 'US',
            'currency' => 'USD',
        ])->assertRedirect();

        $method = $freelancer->payoutMethods()->firstOrFail();

        $this->actingAs($freelancer)->post(route('wallet.withdrawals.store'), [
            'payout_method_id' => $method->id,
            'amount' => 500,
        ])->assertRedirect();

        $withdrawal = WithdrawalRequest::query()->firstOrFail();
        $this->assertSame(WithdrawalRequest::STATUS_PENDING, $withdrawal->status);

        $this->actingAs($admin)->patch(route('admin.withdrawals.update', $withdrawal), [
            'status' => WithdrawalRequest::STATUS_APPROVED,
            'admin_notes' => 'Identity and payout details verified.',
        ])->assertRedirect();

        $this->assertSame(WithdrawalRequest::STATUS_APPROVED, $withdrawal->fresh()->status);
    }

    public function test_freelancer_cannot_withdraw_more_than_available_balance(): void
    {
        [, $freelancer] = $this->projectParticipants();
        $method = $freelancer->payoutMethods()->create([
            'type' => 'bank',
            'account_name' => 'Demo Freelancer',
            'bank_name' => 'Demo Bank',
            'account_number' => '1234567890',
            'account_last_four' => '7890',
            'country' => 'US',
            'currency' => 'USD',
        ]);

        $this->actingAs($freelancer)->post(route('wallet.withdrawals.store'), [
            'payout_method_id' => $method->id,
            'amount' => 100,
        ])->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('withdrawal_requests', 0);
    }

    public function test_accepting_a_proposal_creates_starter_milestones_and_opens_the_order(): void
    {
        $client = $this->user(User::ROLE_CLIENT, 'hire-client@example.test');
        $freelancer = $this->user(User::ROLE_FREELANCER, 'hire-freelancer@example.test');
        $project = $this->project($client);

        $proposal = Proposal::create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
            'cover_letter' => 'I can deliver this with clear milestones and weekly updates.',
            'proposed_amount' => 1000,
            'proposed_duration_days' => 20,
            'status' => Proposal::STATUS_PENDING,
        ]);

        $this->actingAs($client)
            ->post(route('client.proposals.accept', $proposal))
            ->assertRedirect(route('orders.show', $project));

        $this->assertSame(Project::STATUS_IN_PROGRESS, $project->fresh()->status);
        $this->assertSame(3, $project->milestones()->count());
        $this->assertDatabaseHas('transactions', [
            'project_id' => $project->id,
            'type' => Transaction::TYPE_ESCROW_HOLD,
            'status' => Transaction::STATUS_COMPLETED,
        ]);
    }

    public function test_cancelling_an_order_refunds_unreleased_escrow_holds(): void
    {
        [$client, $freelancer, $project] = $this->projectParticipants();
        $milestone = $project->milestones()->create([
            'title' => 'Kickoff',
            'description' => 'Start work',
            'amount' => 250,
            'due_date' => now()->addWeek(),
            'order_index' => 1,
            'status' => Milestone::STATUS_PENDING,
        ]);

        Transaction::create([
            'project_id' => $project->id,
            'milestone_id' => $milestone->id,
            'payer_id' => $client->id,
            'payee_id' => $freelancer->id,
            'amount' => 250,
            'type' => Transaction::TYPE_ESCROW_HOLD,
            'status' => Transaction::STATUS_COMPLETED,
            'description' => 'Held for kickoff',
        ]);

        $this->actingAs($client)
            ->post(route('orders.cancel', $project))
            ->assertRedirect(route('orders.index', ['tab' => 'cancelled']));

        $this->assertSame(Project::STATUS_CANCELLED, $project->fresh()->status);
        $this->assertDatabaseHas('transactions', [
            'milestone_id' => $milestone->id,
            'type' => Transaction::TYPE_REFUND,
            'status' => Transaction::STATUS_COMPLETED,
        ]);
    }

    public function test_admin_dispute_resolution_can_refund_milestone_escrow(): void
    {
        [$client, $freelancer, $project] = $this->projectParticipants();
        $admin = $this->user(User::ROLE_ADMIN, 'dispute-admin@example.test');
        $milestone = $project->milestones()->create([
            'title' => 'Disputed work',
            'description' => 'Needs mediation',
            'amount' => 400,
            'due_date' => now()->addDays(5),
            'order_index' => 1,
            'status' => Milestone::STATUS_SUBMITTED,
        ]);

        Transaction::create([
            'project_id' => $project->id,
            'milestone_id' => $milestone->id,
            'payer_id' => $client->id,
            'payee_id' => $freelancer->id,
            'amount' => 400,
            'type' => Transaction::TYPE_ESCROW_HOLD,
            'status' => Transaction::STATUS_COMPLETED,
            'description' => 'Held for disputed work',
        ]);

        $dispute = Dispute::create([
            'project_id' => $project->id,
            'milestone_id' => $milestone->id,
            'opened_by' => $client->id,
            'against_user_id' => $freelancer->id,
            'reason' => 'Scope disagreement',
            'description' => 'The submitted work does not match the agreed milestone scope.',
            'status' => Dispute::STATUS_OPEN,
        ]);

        $this->actingAs($admin)->post(route('admin.disputes.resolve', $dispute), [
            'status' => Dispute::STATUS_RESOLVED,
            'financial_action' => 'refund',
            'admin_notes' => 'Refunding the disputed milestone after reviewing both sides.',
        ])->assertRedirect(route('admin.disputes.index'));

        $this->assertDatabaseHas('transactions', [
            'milestone_id' => $milestone->id,
            'type' => Transaction::TYPE_REFUND,
            'status' => Transaction::STATUS_COMPLETED,
        ]);
    }

    public function test_hired_projects_cannot_be_deleted_by_clients(): void
    {
        [$client, , $project] = $this->projectParticipants();

        $this->actingAs($client)
            ->delete(route('client.projects.destroy', $project))
            ->assertForbidden();

        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    private function projectParticipants(string $status = Project::STATUS_IN_PROGRESS): array
    {
        $client = $this->user(User::ROLE_CLIENT, 'client'.uniqid().'@example.test');
        $freelancer = $this->user(User::ROLE_FREELANCER, 'freelancer'.uniqid().'@example.test');
        $project = $this->project($client, $freelancer, $status);

        return [$client, $freelancer, $project];
    }

    private function user(string $role, string $email): User
    {
        return User::factory()->create([
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function project(User $client, ?User $freelancer = null, string $status = Project::STATUS_OPEN): Project
    {
        return Project::create([
            'client_id' => $client->id,
            'freelancer_id' => $freelancer?->id,
            'title' => 'Test marketplace project',
            'description' => 'A project used to verify marketplace features.',
            'budget_min' => 500,
            'budget_max' => 1000,
            'deadline' => now()->addMonth(),
            'status' => $status,
            'category' => 'Development',
        ]);
    }
}
