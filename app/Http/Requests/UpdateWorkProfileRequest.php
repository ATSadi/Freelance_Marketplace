<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'bio' => ['required', 'string', 'max:2000'],
            'phone' => ['required', 'string', 'max:30'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ];

        if ($user->role === User::ROLE_CLIENT) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['skills'] = ['nullable', 'string', 'max:2000'];
            $rules['hourly_rate'] = ['nullable', 'numeric', 'min:0'];
        }

        if ($user->role === User::ROLE_FREELANCER) {
            $rules['skills'] = ['required', 'string', 'max:2000'];
            $rules['hourly_rate'] = ['required', 'numeric', 'min:0.01'];
            $rules['company_name'] = ['nullable', 'string', 'max:255'];
        }

        if ($user->role === User::ROLE_ADMIN) {
            $rules['bio'] = ['nullable', 'string', 'max:2000'];
            $rules['phone'] = ['nullable', 'string', 'max:30'];
            $rules['skills'] = ['nullable', 'string', 'max:2000'];
            $rules['hourly_rate'] = ['nullable', 'numeric', 'min:0'];
            $rules['company_name'] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    /**
     * Custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'company name',
            'hourly_rate' => 'hourly rate',
            'profile_photo' => 'profile photo',
        ];
    }
}
