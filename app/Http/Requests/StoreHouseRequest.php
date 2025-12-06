<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHouseRequest extends FormRequest
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
            'country'=>'required|string',
            'city'=>'required|string',
            'category'=>'required|string',
            'bedrooms'=>'required|integer',
            'bathrooms'=>'required|integer',
            'livingrooms'=>'required|integer',
            'area'=>'required|numeric',
            'price'=>'required|numeric',
            'title'=>'required|string|unique:houses,title',
            'mainImage'=>'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'descreption'=>'nullable|string',


        ];
    }
}
