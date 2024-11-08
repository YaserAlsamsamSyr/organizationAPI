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
        return [
            'name'=> ['required', 'string', 'max:255'],
            'address'=> ['required', 'string', 'max:500'],
            'logo'=>['nullable','image','mimes:jpeg,jpg,png,gif'], 
            'summary'=>['required', 'string', 'max:600'],
            'start_At'=>['required','date'],
            'end_At'=>['required','date'],
            'benefitDir'=>['required', 'string', 'max:15','regex:/^[0-9]+$/'],
            'benefitUnd'=>['required', 'string', 'max:15','regex:/^[0-9]+$/'],
            'activities'=>['required', 'string', 'max:1000'],
            'rate'=>['nullable', 'string', 'max:15','regex:/^[0-9]+$/'],
            'pdfURL'=>['nullable',"mimes:pdf","max:100000"],
            'videoURL'=>['nullable', 'string', 'max:1000']
        ];
    }
}
