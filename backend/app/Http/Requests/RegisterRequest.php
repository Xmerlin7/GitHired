<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(
            $this->commonRules(),
            $this->employerRules(),
            $this->candidateRules(),
        );
    }

    /**
     * Rules shared by all users
     */
    protected function commonRules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:15',
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'role' => [
                'required',
                new Enum(UserRole::class),
                // Rule::in([
                //     UserRole::CANDIDATE,
                //     UserRole::EMPLOYER,
                // ]),
            ],
        ];
    }

    /**
     * Employer-only rules
     */
    protected function employerRules(): array
    {
        return [

            'company_name' => [
                'exclude_if:role,candidate',
                'nullable',
                'string',
                'max:50',
            ],

            'company_bio' => [
                'exclude_if:role,candidate',
                'nullable',
                'string',
                'max:1000',
            ],

            'website' => [
                'exclude_if:role,candidate',
                'nullable',
                'url',
            ],

            'company_logo_url' => [
                'exclude_if:role,candidate',
                'nullable',
                'url',
            ],
        ];
    }

    /**
     * Candidate-only rules
     */
    protected function candidateRules(): array
    {
        return [

            'resume_url' => [
                'exclude_if:role,employer',
                'nullable',
                'url',
            ],

            'phone_number' => [
                'exclude_if:role,employer',
                'nullable',
                'string',
                'max:20',
            ],

            'portfolio_link' => [
                'exclude_if:role,employer',
                'nullable',
                'url',
            ],

            'years_experience' => [
                'exclude_if:role,employer',
                'nullable',
                'integer',
                'min:0',
                'max:50',
            ],

            'skills' => [
                'exclude_if:role,employer',
                'nullable',
                'array',
            ],

            'skills.*' => [
                'exclude_if:role,employer',
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }
}
