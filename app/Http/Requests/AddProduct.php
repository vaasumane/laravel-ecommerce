<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProduct extends FormRequest
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
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'product_images.*' =>'required|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'product_name.required' => 'The product name field is required.',
            'product_name.string' => 'The product name must be a string.',
            'product_name.max' => 'The product name may not be greater than :max characters.',

            'product_price.required' => 'The product price field is required.',
            'product_price.numeric' => 'The product price must be a number.',
            'product_price.min' => 'The product price must be at least :min.',

            'product_images.*.required' => 'Please upload at least one image.',
            'product_images.*.mimes' => 'Only JPEG, PNG, JPG,GIF images are allowed.',
            'product_images.*.max' => 'The image size must not exceed 2MB.',
        ];
    }
}
