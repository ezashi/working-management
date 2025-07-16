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
                foreach ($this->breaks as $index => $break) {
                    if (!empty($break['start_time']) || !empty($break['end_time'])) {
                        if (empty($break['start_time']) && !empty($break['end_time'])) {
                            $validator->errors()->add("breaks.{$index}.start_time", '休憩時間が勤務時間外です');
                            continue;
                        }
                        if (isset($break['start_time']) && isset($break['end_time'])) {
                            if ($break['start_time'] < $this->check_in ||
                                $break['end_time'] > $this->check_out ||
                                $break['start_time'] > $this->check_out ||
                                $break['end_time'] < $this->check_in) {
                                    $validator->errors()->add("breaks.{$index}", '休憩時間が勤務時間外です');
                                    break;
                            }
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
