<?php
// app/Http/Requests/RegisterRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
         return [
            'first_name'     => 'required|string|max:60',
            'last_name'      => 'required|string|max:60',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|confirmed|min:8',
            'phone'          => 'nullable|string|max:30',
            'country'        => 'nullable|string|max:80',
            'sport_type'     => 'nullable|string|max:80',
        ];
    }
}
