<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreWorkedHourRequest extends FormRequest
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
            'task' => 'nullable|string',
            'hours' => 'nullable|integer|min:0|max:24',
            'minutes' => 'nullable|integer|min:0|max:59',
            'date' => 'nullable|date|date_format:Y-m-d',
            'bulk_insert' => 'nullable|string',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // If bulk_insert is empty, task is required
            if (empty($this->input('bulk_insert')) && empty($this->input('task'))) {
                $validator->errors()->add('task', 'Task title is required when not using bulk insert.');
            }
        });
    }
}
