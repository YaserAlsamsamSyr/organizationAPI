<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use App\Models\User;

class OrganizationRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes','required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['sometimes','required','confirmed', Rules\Password::defaults()],
            "experience"=>['required','regex:/^[0-9]{1,10}$/'],
            "details"=>['required', 'string', 'max:800'],
            "skils"=>['required', 'string', 'max:1000'],
            "logo"=>['nullable','image','mimes:jpeg,jpg,png,gif'],
            "view"=>['required', 'string', 'max:700'],
            "message"=>['required', 'string', 'max:800'],
            "number"=>['required', 'string', 'max:15','regex:/^[0-9]+$/'],
            "socials"=>['required', 'string', 'max:1000'],
            "address"=>['required', 'string', 'max:600'],
            "phone"=>['required', 'string', 'max:15','regex:/^[0-9]+$/'],
            "complaints"=>['required', 'string', 'max:1000'],
            "suggests"=>['required', 'string', 'max:1000'],
            
        ];
    }
}
