<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckConfirmationCodeRequest extends FormRequest
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
            'customerId' => ['required', 'integer'],
            'code' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'customerId.required' => 'Поле id обязательно для заполнения',
            'customerId.integer' => 'Поле id должно содержать только цифры',
            'code.required' => 'Введите код присланный в смс',
            'code.integer' => 'Код должен содержать только цифры',
        ];
    }
}
