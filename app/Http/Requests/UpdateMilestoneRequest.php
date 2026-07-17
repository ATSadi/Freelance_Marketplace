<?php

namespace App\Http\Requests;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $milestone = $this->route('milestone');

        return $user instanceof User
            && $milestone instanceof Milestone
            && $user->role === User::ROLE_CLIENT
            && $milestone->project->client_id === $user->id
            && $milestone->project->status === Project::STATUS_IN_PROGRESS;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'due_date' => ['required', 'date'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Ensure milestone amounts don't exceed the agreed project budget
     * (excluding the milestone currently being edited).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Milestone $milestone */
            $milestone = $this->route('milestone');
            $project = $milestone->project;

            $otherTotal = (float) $project->milestones()
                ->where('id', '!=', $milestone->id)
                ->sum('amount');

            $newAmount = (float) $this->input('amount', 0);
            $remaining = $project->agreedAmount() - $otherTotal;

            if ($newAmount > $remaining) {
                $validator->errors()->add(
                    'amount',
                    'The total of all milestones cannot exceed the agreed budget of $'
                        .number_format($project->agreedAmount(), 2)
                        .'. Available for this milestone: $'.number_format($remaining, 2).'.'
                );
            }
        });
    }
}
