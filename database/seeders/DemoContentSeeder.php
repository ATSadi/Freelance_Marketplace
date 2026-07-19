<?php

namespace Database\Seeders;

use App\Models\Dispute;
use App\Models\Message;
use App\Models\Milestone;
use App\Models\PayoutMethod;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\StripePayment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawalRequest;
use App\Services\EscrowService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $escrow = app(EscrowService::class);

        $client = $this->user('Elena Vargas', 'client@workvault.test', User::ROLE_CLIENT, [
            'bio' => 'Product lead at a fast-growing SaaS startup. I hire specialists for design, development, and content.',
            'company_name' => 'Nimbus Labs',
            'phone' => '+1 (415) 555-0142',
        ]);

        $freelancer = $this->user('Alex Rivera', 'freelancer@workvault.test', User::ROLE_FREELANCER, [
            'bio' => 'Full-stack engineer specialising in Laravel, Vue, and clean API design. 6+ years shipping production apps.',
            'skills' => 'Laravel, PHP, Vue.js, Tailwind CSS, PostgreSQL, REST APIs',
            'hourly_rate' => 65,
            'phone' => '+1 (206) 555-0199',
        ]);

        // Extra users to enrich the marketplace.
        $clientTwo = $this->user('Priya Sharma', 'priya@workvault.test', User::ROLE_CLIENT, [
            'bio' => 'Founder of an e-commerce brand looking for reliable freelancers.',
            'company_name' => 'Bloom & Co.',
            'phone' => '+44 20 7946 0123',
        ]);

        $freelancerTwo = $this->user('Marcus Lee', 'marcus@workvault.test', User::ROLE_FREELANCER, [
            'bio' => 'Brand and UI/UX designer. I craft delightful, accessible interfaces.',
            'skills' => 'Figma, UI/UX, Branding, Illustration, Design Systems',
            'hourly_rate' => 55,
            'phone' => '+1 (312) 555-0177',
        ]);

        $freelancerThree = $this->user('Sofia Rossi', 'sofia@workvault.test', User::ROLE_FREELANCER, [
            'bio' => 'SEO copywriter and content strategist for tech and lifestyle brands.',
            'skills' => 'Copywriting, SEO, Content Strategy, Editing',
            'hourly_rate' => 45,
            'phone' => '+39 06 5555 0164',
        ]);

        // Only build the project graph once.
        if ($client->projects()->exists()) {
            $this->seedMarketplaceFeatures($client, $freelancer, $clientTwo, $freelancerTwo, $freelancerThree);

            return;
        }

        // ---- Open projects (visible in Browse) ----
        $this->project($client, [
            'title' => 'Build a subscription billing dashboard',
            'description' => 'We need a responsive billing dashboard integrating with our existing API. Charts, invoices, and plan management. Laravel + Vue preferred.',
            'category' => 'Web Development',
            'budget_min' => 2000, 'budget_max' => 4000,
            'deadline' => now()->addDays(28), 'status' => Project::STATUS_OPEN,
        ]);

        $openDesign = $this->project($clientTwo, [
            'title' => 'Brand identity & landing page design',
            'description' => 'Looking for a designer to create a fresh brand identity (logo, palette, type) and a high-converting landing page in Figma.',
            'category' => 'Design',
            'budget_min' => 1200, 'budget_max' => 2500,
            'deadline' => now()->addDays(21), 'status' => Project::STATUS_OPEN,
        ]);

        $this->project($clientTwo, [
            'title' => 'SEO blog content — 8 articles',
            'description' => 'Need 8 well-researched, SEO-optimised blog posts (1200-1500 words) for a lifestyle e-commerce brand.',
            'category' => 'Writing',
            'budget_min' => 600, 'budget_max' => 1000,
            'deadline' => now()->addDays(30), 'status' => Project::STATUS_OPEN,
        ]);

        $this->project($client, [
            'title' => 'Mobile app QA & bug fixing',
            'description' => 'Flutter app needs a thorough QA pass and a batch of bug fixes before release.',
            'category' => 'Web Development',
            'budget_min' => 800, 'budget_max' => 1500,
            'deadline' => now()->addDays(14), 'status' => Project::STATUS_OPEN,
        ]);

        // Pending proposals from the demo freelancer on open projects.
        $this->proposal($openDesign, $freelancerTwo, 'I love this brief — my portfolio has several SaaS rebrands. Ready to start immediately.', 1900, 18, Proposal::STATUS_PENDING);

        // ---- In-progress project with milestones + escrow ----
        $inProgress = $this->project($client, [
            'title' => 'Company website redesign (Laravel)',
            'description' => 'Full redesign and rebuild of our marketing site with a CMS-lite admin, blog, and contact flow. Milestone-based delivery.',
            'category' => 'Web Development',
            'budget_min' => 3000, 'budget_max' => 5000,
            'deadline' => now()->addDays(40), 'status' => Project::STATUS_IN_PROGRESS,
            'freelancer_id' => $freelancer->id,
        ]);

        $accepted = $this->proposal($inProgress, $freelancer, 'I can deliver this in three clear milestones with weekly demos. Estimate attached.', 4200, 35, Proposal::STATUS_ACCEPTED);
        $this->proposal($inProgress, $freelancerThree, 'I can handle all the site copy alongside the build.', 3800, 40, Proposal::STATUS_REJECTED);

        // Milestone 1 — paid
        $m1 = $inProgress->milestones()->create([
            'title' => 'Design & wireframes', 'description' => 'Approved wireframes and hi-fi mockups for all pages.',
            'amount' => 1400, 'due_date' => now()->subDays(12), 'order_index' => 1,
            'status' => Milestone::STATUS_PAID,
            'started_at' => now()->subDays(25), 'submitted_at' => now()->subDays(15),
            'submission_notes' => 'All mockups delivered via Figma and approved.',
            'approved_at' => now()->subDays(13),
        ]);
        $escrow->hold($m1);
        $inProgress->transactions()->create([
            'milestone_id' => $m1->id,
            'payer_id' => $client->id, 'payee_id' => $freelancer->id,
            'amount' => $m1->amount, 'type' => Transaction::TYPE_ESCROW_RELEASE,
            'status' => Transaction::STATUS_COMPLETED,
            'description' => 'Escrow released for milestone: '.$m1->title,
        ]);

        // Milestone 2 — submitted, awaiting approval (held in escrow)
        $m2 = $inProgress->milestones()->create([
            'title' => 'Frontend build', 'description' => 'Responsive Blade/Tailwind implementation of all pages.',
            'amount' => 1600, 'due_date' => now()->addDays(6), 'order_index' => 2,
            'status' => Milestone::STATUS_SUBMITTED,
            'started_at' => now()->subDays(10), 'submitted_at' => now()->subDays(1),
            'submission_notes' => 'Deployed to staging. Please review the homepage and blog.',
        ]);
        $escrow->hold($m2);

        // Milestone 3 — pending
        $m3 = $inProgress->milestones()->create([
            'title' => 'CMS admin & launch', 'description' => 'Admin panel, content migration, and production launch.',
            'amount' => 1200, 'due_date' => now()->addDays(20), 'order_index' => 3,
            'status' => Milestone::STATUS_PENDING,
        ]);
        $escrow->hold($m3);

        // ---- Completed project ----
        $completed = $this->project($client, [
            'title' => 'API integration for CRM sync',
            'description' => 'Two-way sync between our app and a third-party CRM via webhooks and a scheduled job.',
            'category' => 'Web Development',
            'budget_min' => 1500, 'budget_max' => 2200,
            'deadline' => now()->subDays(5), 'status' => Project::STATUS_COMPLETED,
            'freelancer_id' => $freelancer->id,
        ]);
        $this->proposal($completed, $freelancer, 'Done plenty of CRM integrations — I know the pitfalls. Fixed timeline.', 2000, 20, Proposal::STATUS_ACCEPTED);

        $cm = $completed->milestones()->create([
            'title' => 'Full integration & tests', 'description' => 'Complete sync, webhook handling, and automated tests.',
            'amount' => 2000, 'due_date' => now()->subDays(8), 'order_index' => 1,
            'status' => Milestone::STATUS_PAID,
            'started_at' => now()->subDays(30), 'submitted_at' => now()->subDays(10),
            'submission_notes' => 'Shipped and verified in production.',
            'approved_at' => now()->subDays(8),
        ]);
        $escrow->hold($cm);
        $completed->transactions()->create([
            'milestone_id' => $cm->id,
            'payer_id' => $client->id, 'payee_id' => $freelancer->id,
            'amount' => $cm->amount, 'type' => Transaction::TYPE_ESCROW_RELEASE,
            'status' => Transaction::STATUS_COMPLETED,
            'description' => 'Escrow released for milestone: '.$cm->title,
        ]);

        // ---- An open dispute on the in-progress project ----
        $inProgress->disputes()->create([
            'milestone_id' => $m2->id,
            'opened_by' => $client->id,
            'against_user_id' => $freelancer->id,
            'reason' => 'Milestone scope mismatch',
            'description' => 'The submitted frontend build is missing the blog category filters that were part of milestone 2. Requesting review before approval.',
            'status' => Dispute::STATUS_OPEN,
        ]);

        $this->seedMarketplaceFeatures($client, $freelancer, $clientTwo, $freelancerTwo, $freelancerThree);
    }

    private function user(string $name, string $email, string $role, array $profile): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'role' => $role,
                'email_verified_at' => Carbon::now(),
                'is_active' => true,
            ]
        );

        $user->profile()->updateOrCreate(['user_id' => $user->id], $profile);

        return $user;
    }

    private function project(User $client, array $attributes): Project
    {
        return $client->projects()->create($attributes);
    }

    private function proposal(Project $project, User $freelancer, string $cover, float $amount, int $days, string $status): Proposal
    {
        return $project->proposals()->create([
            'freelancer_id' => $freelancer->id,
            'cover_letter' => $cover,
            'proposed_amount' => $amount,
            'proposed_duration_days' => $days,
            'status' => $status,
        ]);
    }

    private function seedMarketplaceFeatures(
        User $client,
        User $freelancer,
        User $clientTwo,
        User $freelancerTwo,
        User $freelancerThree,
    ): void {
        $extraFreelancers = [
            ['Aisha Rahman', 'aisha@workvault.test', 'React, Next.js, TypeScript, Node.js', 72],
            ['Daniel Kim', 'daniel@workvault.test', 'Flutter, iOS, Android, Firebase', 68],
            ['Emma Wilson', 'emma@workvault.test', 'Digital Marketing, PPC, Analytics, Growth', 58],
            ['Noah Williams', 'noah@workvault.test', 'Video Editing, Motion Graphics, After Effects', 52],
            ['Lina Haddad', 'lina@workvault.test', 'WordPress, WooCommerce, PHP, SEO', 48],
        ];

        foreach ($extraFreelancers as [$name, $email, $skills, $rate]) {
            $this->user($name, $email, User::ROLE_FREELANCER, [
                'bio' => 'Independent specialist focused on clear communication and reliable milestone delivery.',
                'skills' => $skills,
                'hourly_rate' => $rate,
                'phone' => '+1 (555) '.random_int(100, 999).'-'.random_int(1000, 9999),
            ]);
        }

        $extraProjects = [
            ['Design a cross-platform fitness app', 'Mobile Development', 2500, 4800],
            ['Create an investor pitch deck', 'Design', 700, 1400],
            ['Shopify conversion-rate optimization', 'E-commerce', 900, 1800],
            ['Product launch video and motion graphics', 'Video & Animation', 1200, 2600],
            ['Set up analytics and conversion tracking', 'Digital Marketing', 600, 1200],
            ['Build a customer support knowledge base', 'Writing', 500, 950],
            ['Migrate legacy PHP app to Laravel', 'Web Development', 3000, 6500],
            ['Social media campaign for product launch', 'Digital Marketing', 800, 1600],
        ];

        foreach ($extraProjects as [$title, $category, $min, $max]) {
            Project::firstOrCreate(
                ['client_id' => $clientTwo->id, 'title' => $title],
                [
                    'description' => 'Looking for a reliable freelancer with clear deliverables, collaborative milestones, and timely feedback.',
                    'category' => $category,
                    'budget_min' => $min,
                    'budget_max' => $max,
                    'deadline' => now()->addDays(random_int(15, 60)),
                    'status' => Project::STATUS_OPEN,
                ]
            );
        }

        $inProgress = Project::query()->where('title', 'Company website redesign (Laravel)')->first();
        $completed = Project::query()->where('title', 'API integration for CRM sync')->first();

        $payoutMethod = PayoutMethod::updateOrCreate(
            ['user_id' => $freelancer->id, 'account_last_four' => '4242'],
            [
                'type' => 'bank',
                'account_name' => $freelancer->name,
                'bank_name' => 'Chase Business Checking',
                'account_number' => '0000123456784242',
                'routing_number' => '110000000',
                'country' => 'US',
                'currency' => 'USD',
                'is_default' => true,
            ]
        );
        WithdrawalRequest::firstOrCreate(
            ['user_id' => $freelancer->id, 'payout_method_id' => $payoutMethod->id, 'amount' => 500],
            ['status' => WithdrawalRequest::STATUS_PENDING]
        );
        WithdrawalRequest::firstOrCreate(
            ['user_id' => $freelancer->id, 'payout_method_id' => $payoutMethod->id, 'amount' => 900],
            [
                'status' => WithdrawalRequest::STATUS_PAID,
                'admin_notes' => 'Wire transfer confirmed — ref WV-PAYOUT-1001',
                'processed_at' => now()->subDays(10),
            ]
        );

        WithdrawalRequest::query()
            ->where('user_id', $freelancer->id)
            ->where('amount', 900)
            ->where('status', WithdrawalRequest::STATUS_PAID)
            ->update(['admin_notes' => 'Wire transfer confirmed — ref WV-PAYOUT-1001']);

        if ($inProgress) {
            $conversation = [
                [$client->id, 'Welcome aboard! I have added the brand guide and approved sitemap to the project notes.'],
                [$freelancer->id, 'Thanks! I reviewed both. I will share the first design direction by tomorrow afternoon.'],
                [$client->id, 'Perfect. Please prioritize the pricing and case-study pages for the first review.'],
                [$freelancer->id, 'Will do. The responsive header and pricing components are already in progress.'],
                [$freelancer->id, 'Frontend milestone is ready on staging. I included notes for each completed page.'],
            ];

            foreach ($conversation as $index => [$sender, $body]) {
                Message::firstOrCreate(
                    ['project_id' => $inProgress->id, 'sender_id' => $sender, 'body' => $body],
                    ['created_at' => now()->subHours(12 - $index), 'updated_at' => now()->subHours(12 - $index)]
                );
            }

            $client->savedProjects()->syncWithoutDetaching(
                Project::query()->where('status', Project::STATUS_OPEN)->take(2)->pluck('id')
            );
            $freelancer->savedProjects()->syncWithoutDetaching(
                Project::query()->where('status', Project::STATUS_OPEN)->take(5)->pluck('id')
            );

            $milestone = $inProgress->milestones()->first();
            if ($milestone) {
                StripePayment::firstOrCreate(
                    ['milestone_id' => $milestone->id, 'user_id' => $client->id],
                    [
                        'stripe_session_id' => 'cs_test_demo_workvault',
                        'stripe_payment_intent_id' => 'pi_test_demo_workvault',
                        'amount' => (int) round((float) $milestone->amount * 100),
                        'currency' => 'usd',
                        'status' => StripePayment::STATUS_PAID,
                    ]
                );
            }
        }

        if ($completed) {
            Review::firstOrCreate(
                ['project_id' => $completed->id, 'reviewer_id' => $client->id],
                ['reviewee_id' => $freelancer->id, 'rating' => 5, 'comment' => 'Excellent delivery. The integration was reliable, well tested, and documented clearly.']
            );
            Review::firstOrCreate(
                ['project_id' => $completed->id, 'reviewer_id' => $freelancer->id],
                ['reviewee_id' => $client->id, 'rating' => 5, 'comment' => 'Clear scope, fast feedback, and professional communication throughout the project.']
            );
        }

        $cancelled = Project::firstOrCreate(
            ['client_id' => $client->id, 'title' => 'Analytics dashboard maintenance'],
            [
                'freelancer_id' => $freelancer->id,
                'description' => 'A monthly analytics dashboard maintenance contract that was cancelled by mutual agreement after priorities changed.',
                'category' => 'Web Development',
                'budget_min' => 600,
                'budget_max' => 900,
                'deadline' => now()->subDays(18),
                'status' => Project::STATUS_CANCELLED,
            ]
        );
        Proposal::firstOrCreate(
            ['project_id' => $cancelled->id, 'freelancer_id' => $freelancer->id],
            [
                'cover_letter' => 'Monthly maintenance and reporting support.',
                'proposed_amount' => 750,
                'proposed_duration_days' => 30,
                'status' => Proposal::STATUS_ACCEPTED,
            ]
        );
        $cancelled->milestones()->firstOrCreate(
            ['title' => 'Monthly reporting setup'],
            [
                'description' => 'Configure recurring analytics reports and dashboard health checks.',
                'amount' => 750,
                'due_date' => now()->subDays(18),
                'order_index' => 1,
                'status' => Milestone::STATUS_PENDING,
            ]
        );

        $brandProject = Project::firstOrCreate(
            ['client_id' => $clientTwo->id, 'title' => 'Completed brand refresh'],
            [
                'freelancer_id' => $freelancerTwo->id,
                'description' => 'Brand identity refresh delivered successfully.',
                'category' => 'Design',
                'budget_min' => 1200,
                'budget_max' => 1800,
                'deadline' => now()->subDays(20),
                'status' => Project::STATUS_COMPLETED,
            ]
        );
        Review::firstOrCreate(
            ['project_id' => $brandProject->id, 'reviewer_id' => $clientTwo->id],
            ['reviewee_id' => $freelancerTwo->id, 'rating' => 4, 'comment' => 'Strong creative thinking and very responsive collaboration.']
        );

        $contentProject = Project::firstOrCreate(
            ['client_id' => $client->id, 'title' => 'Completed product content launch'],
            [
                'freelancer_id' => $freelancerThree->id,
                'description' => 'Launch copy and content strategy delivered successfully.',
                'category' => 'Writing',
                'budget_min' => 800,
                'budget_max' => 1200,
                'deadline' => now()->subDays(12),
                'status' => Project::STATUS_COMPLETED,
            ]
        );
        Review::firstOrCreate(
            ['project_id' => $contentProject->id, 'reviewer_id' => $client->id],
            ['reviewee_id' => $freelancerThree->id, 'rating' => 5, 'comment' => 'Thoughtful content strategy and polished final copy.']
        );
    }
}
