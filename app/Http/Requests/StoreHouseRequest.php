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
            'category'=>'required|string',
            'bedrooms'=>'required|regex:/^[0-9]+$/',
            'bathrooms'=>'required|regex:/^[0-9]+$/',
            'livingrooms'=>'required|regex:/^[0-9]+$/',
            'area'=>'required|regex:/^[0-9]+$/',
            'day_price'=>'required|regex:/^[0-9]+$/',
            'title'=>'required|string|unique:houses,title',
            'mainImage'=>'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'descreption'=>'nullable|string',


        ];
    }
}
