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
            'category'=>'nullable|string',
            'bedrooms'=>'nullable|regex:/^[0-9]+$/',
            'bathrooms'=>'nullable|regex:/^[0-9]+$/',
            'livingrooms'=>'nullable|regex:/^[0-9]+$/',
            'area'=>'nullable|regex:/^[0-9]+$/',
            'day_price'=>'nullable|regex:/^[0-9]+$/',
            'title'=>'nullable|string|unique:houses,title,' . $id,
            'mainImage'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'descreption'=>'nullable|string',

        ];
    }
}
