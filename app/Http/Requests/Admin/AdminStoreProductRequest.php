<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStoreProductRequest extends FormRequest
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
        $productId = $this->route('product') ? $this->route('product')->id : null;
        return [
            'name' => 'required|string|max:255',

            // Nếu update thì ignore ID hiện tại
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],

            'category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'content' => 'required|string', // SỬA: Content là bắt buộc theo Migration
            'short_description' => 'nullable|string|max:255', // SỬA: short_description theo Migration
            'is_featured' => 'nullable|boolean', // SỬA: is_featured theo Migration

            // File ảnh
            'thumbnail' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048'
            ],
        ];
    }

    /**
     * Custom messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'price.required' => 'Giá bán không được để trống.',
            'content.required' => 'Nội dung sản phẩm không được để trống.',
            'thumbnail.image' => 'File tải lên phải là hình ảnh.',
            'thumbnail.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ];
    }
}
