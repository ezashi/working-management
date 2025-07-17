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
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i', 'after:check_in'],
            'breaks' => ['nullable', 'array'],
            'breaks.*.start_time' => ['nullable', 'date_format:H:i'],
            'breaks.*.end_time' => ['nullable', 'date_format:H:i'],
            'note' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages()
    {
        return [
            'check_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->check_in && $this->check_out && $this->breaks) {
                $checkIn = strtotime($this->check_in);
                $checkOut = strtotime($this->check_out);

                foreach ($this->breaks as $index => $break) {
                    // 休憩開始時間または終了時間が入力されている場合
                    if (!empty($break['start_time']) || !empty($break['end_time'])) {
                        $breakStart = strtotime($break['start_time']);
                        $breakEnd = strtotime($break['end_time']);
                        // 休憩時間が勤務時間外の場合
                        if ($breakStart < $checkIn || $breakEnd > $checkOut) {
                            $validator->errors()->add("breaks.{$index}", '休憩時間が勤務時間外です');
                            continue;
                        }
                    }
                }
            }
        });
    }

    protected function prepareForValidation()
    {
        // 空の休憩時間を除外
        if ($this->has('breaks')) {
            $breaks = collect($this->breaks)->filter(function ($break) {
                return !empty($break['start_time']) || !empty($break['end_time']);
            })->values()->toArray();

            $this->merge([
                'breaks' => empty($breaks) ? null : $breaks
            ]);
        }
    }
}
