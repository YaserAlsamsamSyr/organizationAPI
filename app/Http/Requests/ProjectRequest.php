<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
        $rules = [
            'name'=> ['required', 'string'],
            'address'=> ['required', 'string'],
            'logo'=>['nullable','image','mimes:jpeg,jpg,png,gif'], 
            'videoLogo'=>['nullable','image','mimes:jpeg,jpg,png,gif'],
            'start_At'=>['required','date'],
            'end_At'=>['required','date'],
            'benefitDir'=>['required', 'string', 'max:15','regex:/^[0-9]+$/'],
            'benefitUnd'=>['required', 'string', 'max:15','regex:/^[0-9]+$/'],
            'rate'=>['nullable', 'string', 'max:15','regex:/^[0-9]+$/'],
            'pdfURL'=>['nullable',"mimes:pdf"],
            'videoURL'=>['nullable', 'string'],
            "summaries"=>["nullable",'array',"min:1"],
            'summaries.*.text' => ['nullable', 'string'],
            'summaries.*.type' => ['nullable', 'string'],
            "images"=>["nullable",'array',"min:1"],
            "images.*"=>['nullable','image','mimes:jpeg,jpg,png,gif'],
        ];
        return $rules;
    }
}
