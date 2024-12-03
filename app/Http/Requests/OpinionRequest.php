<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpinionRequest extends FormRequest
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
        return [
            'name'=> ['required', 'string', 'max:255'],
            'mac'=>['required', 'string','max:30'],
            'q1'=> ['required', 'string', 'max:600'],
            'q2'=> ['required', 'string', 'max:600'],
            'q3'=> ['required', 'string', 'max:600'],
            'q4'=> ['required', 'string', 'max:600'],
            'q5'=> ['required', 'string', 'max:600'],
            'q6'=> ['required', 'string', 'max:600'],
            'q7'=> ['required', 'string', 'max:600'],
            'q8'=> ['required', 'string', 'max:600'],
            'q9'=> ['required', 'string', 'max:600'],
            'q10'=> ['required', 'string', 'max:600'],
            'q11'=> ['required', 'string', 'max:600'],
            'q12'=> ['required', 'string', 'max:600'],
            'q13'=> ['required', 'string', 'max:600'],
            'q14'=> ['required', 'string', 'max:600'],
            'q15'=> ['required', 'string', 'max:600'],
            'q16'=> ['required', 'string', 'max:600'],
            'q17'=> ['required', 'string', 'max:600'],
            'q18'=> ['required', 'string', 'max:600'],
            'q19'=> ['required', 'string', 'max:600'],
            'q20'=> ['required', 'string', 'max:600']
        ];
    }
}
