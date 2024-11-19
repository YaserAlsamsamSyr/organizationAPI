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
        $rules =  [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes','required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['sometimes','required','confirmed', Rules\Password::defaults()],
            "experience"=>['required','regex:/^[0-9]{1,10}$/'],
            "logo"=>['nullable','image','mimes:jpeg,jpg,png,gif'],
            "view"=>['required', 'string', 'max:700'],
            "message"=>['required', 'string', 'max:800'],
            "address"=>['required', 'string', 'max:600'],
            "phone"=>['required', 'string', 'max:15','regex:/^[0-9]+$/'],
        ];
                 
        foreach($this->request->get('details') as $key => $val)
            $rules['details.'.$key] = ['nullable', 'string', 'max:800']; 
         
        foreach($this->request->get('skils') as $key => $val)
            $rules['skils.'.$key] = ['nullable', 'string', 'max:800']; 
    
        foreach($this->request->get('numbers') as $key => $val){
            $rules['numbers.type'] = ['nullable', 'string', 'max:400'];
            $rules['numbers.number'] = ['nullable', 'string', 'regex:/^[0-9]+$/'];
        } 

        foreach($this->request->get('socials') as $key => $val){
            $rules['socials.type'] = ['nullable', 'string', 'max:80'];
            $rules['socials.url'] = ['nullable', 'string'];
        } 
     
          return $rules;
    }
}
