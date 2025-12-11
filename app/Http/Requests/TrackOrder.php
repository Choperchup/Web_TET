<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackOrder extends FormRequest
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
            'order_id' => 'required|numeric',
            'customer_phone' => 'required|string|max:20',
            'order_id.required' => 'Vui lòng nhập Mã đơn hàng.',
            'order_id.numeric' => 'Mã đơn hàng phải là số.',
            'customer_phone.required' => 'Vui lòng nhập Số điện thoại đã đặt hàng.',
        ];
    }
}
