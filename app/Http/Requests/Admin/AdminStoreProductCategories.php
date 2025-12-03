<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreProductCategories extends FormRequest
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
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:product_categories,slug|max:255',
            'description' => 'nullable|string',
            'name.required' => 'Tên danh mục không được để trống.',
            'slug.unique' => 'Đường dẫn thân thiện (slug) này đã tồn tại.',
        ];
    }
}
