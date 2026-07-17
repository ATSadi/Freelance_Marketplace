<?php

namespace App\Http\Requests;

use App\Models\Milestone;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReviewMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $milestone = $this->route('milestone');

        return $user instanceof User
            && $milestone instanceof Milestone
            && $user->can('review', $milestone);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $requiresFeedback = $this->routeIs('client.milestones.request-changes');

        return [
            'client_feedback' => [
                $requiresFeedback ? 'required' : 'nullable',
                'string',
                'max:2000',
            ],
        ];
    }
}
