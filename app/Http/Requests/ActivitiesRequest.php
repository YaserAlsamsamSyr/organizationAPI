<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivitiesRequest extends FormRequest
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
            'text' => ['required', 'string'],
            'type' => ['required', 'string'],
            'videoUrl' => ['nullable', 'string'],
            'videoImg' => ['nullable','image','mimes:jpeg,jpg,png,gif'],
            'pdf' => ['nullable',"mimes:pdf"],
            'activities.*.images' => ["nullable",'array',"min:1"],
            'activities.*.images.*' => ['required','image','mimes:jpeg,jpg,png,gif'],
        ];
    }
}
