<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomepageRequest extends FormRequest
{
    public function authorize()
    {
        return true; // only admin can access, you can add auth check
    }

    public function rules()
{
    return [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:5000',
    ];
}

}
