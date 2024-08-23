<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:191',
            'email'    => 'required|email|unique:users,email|string|max:191',
            'password' => 'required|string|min:8|max:191',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'ユーザーネームは必須です。',
            'name.string'   => 'ユーザーネームは文字列でなければなりません。',
            'name.max'      => 'ユーザーネームは最大191文字です。',
            'email.required'    => 'メールアドレスは必須です。',
            'email.email'       => 'メールアドレスの形式が正しくありません。',
            'email.unique'      => 'このメールアドレスはすでに使用されています。',
            'email.string'      => 'メールアドレスは文字列でなければなりません。',
            'email.max'         => 'メールアドレスは最大191文字です。',
            'password.required' => 'パスワードは必須です。',
            'password.string'   => 'パスワードは文字列でなければなりません。',
            'password.min'      => 'パスワードは8文字以上でなければなりません。',
            'password.max'      => 'パスワードは最大191文字です。',
        ];
    }
}
