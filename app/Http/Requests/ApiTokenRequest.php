<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiTokenRequest extends FormRequest
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
            'username' => ['required', 'string', 'max:16'],
            'password' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Поле обязательно для заполнения',
            'username.string' => 'Поле должно быть строкой',
            'username.max' => 'Превышено максимальное количество символов',
            'password.required' => 'Поле обязательно для заполнения',
            'password.string' => 'Поле должно быть строкой',
            'password.max' => 'Превышено максимальное количество символов',
        ];
    }
}
