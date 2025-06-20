<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => ['required','string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // バリデーションエラーがない場合のみ認証処理を実行
            if (!$validator->errors()->any()) {
                if (!Auth::attempt($this->only('email', 'password'))) {
                    // 認証失敗時のエラーメッセージを追加
                    throw ValidationException::withMessages([
                        'email' => ['ログイン情報が登録されていません。'],
                    ]);
                }
            }
        });
    }
}
