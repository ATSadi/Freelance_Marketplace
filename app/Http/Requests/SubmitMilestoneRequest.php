<?php

namespace App\Http\Requests;

use App\Models\Milestone;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $milestone = $this->route('milestone');

        return $user instanceof User
            && $milestone instanceof Milestone
            && $user->can('submit', $milestone);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'submission_notes' => ['required', 'string', 'min:10'],
        ];
    }
}
