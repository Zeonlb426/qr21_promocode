<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromocodeRequest extends FormRequest
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
            'promocode' => ['required', 'string', 'max:32'],
        ];
    }

    public function messages(): array
    {
        return [
            'promocode.required' => 'Промокод обязателен для заполнения',
            'promocode.string' => 'Поле промокода должно быть строкой',
            'promocode.max' => 'Количество символов не должно привышать 32',
        ];
    }
}
