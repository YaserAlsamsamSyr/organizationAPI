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
            'date'=> ['required', 'string', 'max:100'],
            'number'=>['required', 'string', 'regex:/^[0-9]+$/'],
            'fullName'=> ['required', 'string', 'max:200'],
            'phone'=>['required', 'string', 'regex:/^[0-9]+$/'],
            'email'=> ['required', 'string', 'max:500','regex:/^[a-zA-Z0-9]+@[a-zA-Z]+.[a-zA-Z]+$/'],
            'address'=> ['required', 'string', 'max:300'],
            'benifit'=> ['required', 'string', 'max:500'],
            'problemDate'=> ['required', 'string', 'max:100'],
            'isPrevious'=> ['required', 'string', 'max:500'],
        ];
        foreach($this->request->get('typeProblems') as $key => $val){
            $rules['typeProblems.type'] = ['nullable', 'string', 'max:400']; 
        }
        return $rules;
    }
}
