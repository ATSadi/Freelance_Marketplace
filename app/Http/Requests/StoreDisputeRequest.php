<?php

namespace App\Http\Requests;

use App\Models\Dispute;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDisputeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $project = $this->route('project');

        return $user instanceof User
            && $project instanceof Project
            && $user->can('create', [Dispute::class, $project]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'milestone_id' => ['nullable', 'exists:milestones,id'],
            'reason' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
        ];
    }
}
