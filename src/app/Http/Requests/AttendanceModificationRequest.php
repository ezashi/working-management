<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceModificationRequest extends FormRequest
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
            'check_in' => ['nullable', 'date_format:H:i', 'before:check_out'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'breaks' => ['nullable', 'array'],
            'breaks.*.start_time' => ['required_with:breaks', 'date_format:H:i', 'before:breaks.*.end_time'],
            'breaks.*.end_time' => ['nullable', 'date_format:H:i'],
            'note' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages()
    {
        return [
            'check_in.before' => '出勤時間もしくは退勤時間が不適切な値です。',
            'breaks.*.start_time.before' => '休憩時間が勤務時間外です。',
            'note.required' => '備考を記入してください',
        ];
    }
}
