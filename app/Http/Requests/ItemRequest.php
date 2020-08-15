<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  => 'required|string',
            'email' => 'required|email',
            'phone'  => 'required|regex:/(\d{0,3})(\d{0,3})(\d{0,4})/',
            'address'  => 'required|string',
            'zip'  => 'required|numeric|min:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:40960',
        ];
    }
}
