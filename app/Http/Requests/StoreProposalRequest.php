<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $project = $this->route('project');

        return $user
            && $user->role === User::ROLE_FREELANCER
            && $project instanceof Project
            && $project->status === Project::STATUS_OPEN;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $project = $this->route('project');

        return [
            'cover_letter' => ['required', 'string', 'min:20'],
            'proposed_amount' => ['required', 'numeric', 'gt:0'],
            'proposed_duration_days' => ['required', 'integer', 'min:1'],
            'project_id' => [
                Rule::unique('proposals', 'project_id')
                    ->where('freelancer_id', $this->user()->id),
            ],
        ];
    }

    /**
     * Merge project id so the unique rule can validate duplicates.
     *
     * @return array<string, mixed>
     */
    protected function prepareForValidation(): void
    {
        $project = $this->route('project');

        if ($project instanceof Project) {
            $this->merge([
                'project_id' => $project->id,
            ]);
        }
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'proposed_amount' => 'proposed amount',
            'proposed_duration_days' => 'proposed duration',
            'project_id' => 'project',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'project_id.unique' => 'You have already submitted a proposal for this project.',
        ];
    }
}
