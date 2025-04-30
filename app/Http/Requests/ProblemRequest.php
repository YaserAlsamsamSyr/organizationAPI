<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProblemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules= [
            'fullName'=> ['required', 'string'],
            'phone'=>['required', 'string', 'regex:/^[0-9]+$/'],
            'email'=> ['nullable', 'string', 'max:500','regex:/^[a-zA-Z0-9]+@[a-zA-Z]+.[a-zA-Z]+$/'],
            'address'=> ['required', 'string'],
            'benifit'=> ['required', 'string'],
            'text'=> ['required', 'string'],
            'problemDate'=> ['required', 'string'],
            'isPrevious'=> ['required', 'string'],
            'typeProblems'=>['nullable','array','min:1'],
            'typeProblems.*.type' => ['nullable', 'string']
        ];
        return $rules;
    }
}
