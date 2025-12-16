<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' kiểm tra 'password_confirmation'
        ];
    }

    /**
     * Thêm logic kiểm tra mật khẩu hiện tại vào Form Request.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = Auth::user();

            if (!Hash::check($this->current_password, $user->password)) {
                $validator->errors()->add('current_password', 'Mật khẩu hiện tại không đúng.');
            }
        });
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Mật khẩu hiện tại không được để trống.',
            'password.required' => 'Mật khẩu mới không được để trống.',
            'password.string' => 'Mật khẩu mới phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
