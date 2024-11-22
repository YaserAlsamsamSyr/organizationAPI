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
            'name'=> ['required', 'string', 'max:255'],
            'address'=> ['required', 'string', 'max:500'],
            'logo'=>['nullable','image','mimes:jpeg,jpg,png,gif'], 
            'start_At'=>['required','date'],
            'end_At'=>['required','date'],
            'benefitDir'=>['required', 'string', 'max:15','regex:/^[0-9]+$/'],
            'benefitUnd'=>['required', 'string', 'max:15','regex:/^[0-9]+$/'],
            'rate'=>['nullable', 'string', 'max:15','regex:/^[0-9]+$/'],
            'pdfURL'=>['nullable',"mimes:pdf"],
            'videoURL'=>['nullable', 'string', 'max:1000'],
            "activities"=>["nullable",'array',"min:1"],
            'activities.*.text' => ['nullable', 'string'],
            'activities.*.type' => ['nullable', 'string', 'max:10000'],
            "summaries"=>["nullable",'array',"min:1"],
            'summaries.*.text' => ['nullable', 'string'],
            'summaries.*.type' => ['nullable', 'string', 'max:10000'] ,
            "images"=>["nullable",'array',"min:1"],
            "images.*"=>['nullable','image','mimes:jpeg,jpg,png,gif'],
        ];
        return $rules;
    }
}
