<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhoneRequest extends FormRequest
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
            'phone' => ['required', 'phone:RU'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Поле телефона обязательно для заполнения',
            'phone.phone' => 'Поле телефона содержит недопустимый номер.',
        ];
    }
}
