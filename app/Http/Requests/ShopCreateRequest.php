<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopCreateRequest extends FormRequest
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
            'shop_categories' => 'required',
            'name' => ['required','string'],
            'phone' => ['required','string'],
            'address' => ['required','string'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'shop_services' => ['required']
        ];
    }
}
