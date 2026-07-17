<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $project = $this->route('project');

        return $user instanceof User
            && $project instanceof Project
            && $user->role === User::ROLE_CLIENT
            && $project->client_id === $user->id
            && $project->status === Project::STATUS_IN_PROGRESS;
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
            'due_date' => ['required', 'date', 'after:today'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Ensure milestone amounts don't exceed the agreed project budget.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Project $project */
            $project = $this->route('project');

            $newAmount = (float) $this->input('amount', 0);
            $remaining = $project->remainingBudget();

            if ($newAmount > $remaining) {
                $validator->errors()->add(
                    'amount',
                    'The total of all milestones cannot exceed the agreed budget of $'
                        .number_format($project->agreedAmount(), 2)
                        .'. Remaining: $'.number_format($remaining, 2).'.'
                );
            }
        });
    }
}
