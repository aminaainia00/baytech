<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHouseRequest extends FormRequest
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
    {   $id=$this->route('id');
        return [
            'country'=>'nullable|string',
            'city'=>'nullable|string',
            'category'=>'nullable|string',
            'bedrooms'=>'nullable|integer',
            'bathrooms'=>'nullable|integer',
            'livingrooms'=>'nullable|integer',
            'area'=>'nullable|numeric',
            'price'=>'nullable|numeric',
            'title'=>'nullable|string|unique:houses,title,' . $id,
            'mainImage'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'descreption'=>'nullable|string',

        ];
    }
}
