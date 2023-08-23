<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'answer' => ['required', 'array', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'answer.required' => 'Ответ обязателен для заполнения',
            'answer.array' => 'Поле ответа должно быть массивом',
            'answer.max' => 'Количество символов не должно привышать 255',
        ];
    }
}
