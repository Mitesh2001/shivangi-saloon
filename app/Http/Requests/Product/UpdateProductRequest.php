<?php

namespace App\Http\Requests\product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => ['required'],
            'purchase_price' => ['required', 'numeric'],
            'sales_price' => ['required', 'numeric'],
            // 'category_id' => ['required'],
            // 'unit_id' => ['required'],
            // 'sku_code' => ['required'],
            'thumbnail' => ['mimes:jpeg,jpg,gif,svg,png', 'max:2048'],
        ];
    }

    // Messages
    public function messages() 
    {
        return [
            'name.required' => 'Please enter product name!',
            'sales_price.required' => 'Please enter sales Price!',
            'sales_price.numeric' => 'Please valid sales Price!',
            'purchase_price.required' => 'Please enter purchase price!',
            'purchase_price.numeric' => 'Please valid purchase price!',
            // 'category_id.required' => 'Please select product category!',
            // 'unit_id.required' => 'Please select unit!',
            // 'sku_code.required' => 'Please enter SKU Code!', 
            'thumbnail.mimes' => 'Image type not supported (supported: jpeg, png, gif, svg)!',
            'thumbnail.max' => 'Thumbnail size can not me more then 2 mb!',
        ];
    }
}
