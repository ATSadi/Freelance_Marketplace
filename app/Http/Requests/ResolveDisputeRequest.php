<?php

namespace App\Http\Requests;

use App\Models\Dispute;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResolveDisputeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $dispute = $this->route('dispute');

        return $user instanceof User
            && $dispute instanceof Dispute
            && $user->can('moderate', $dispute);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([Dispute::STATUS_RESOLVED, Dispute::STATUS_DISMISSED])],
            'admin_notes' => ['required', 'string', 'min:10'],
        ];
    }
}
