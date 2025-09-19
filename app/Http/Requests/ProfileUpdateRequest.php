<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique(User::class)->ignore($this->user()->id)
            ],
            
            'fullname' => [
                'required',
                'string',
                'max:255'
            ],

            'birthdate' => [
                'required',
                'date_format:Y-m-d'
            ],

            'bank_account_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,16}$/',
                Rule::unique(User::class)->ignore($this->user()->id)
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}
