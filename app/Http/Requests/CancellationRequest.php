<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancellationRequest extends FormRequest
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
            '*' => ['required'],
            '*.COUPON_CODE' => ['required', 'max:100'],
            '*.COUPON_STATUS' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'Нет данных для обработки',
            '*.COUPON_CODE.required' => 'COUPON_CODE обязателен для заполнения',
            '*.COUPON_CODE.max' => 'Количество символов не должно привышать 100',
            '*.COUPON_STATUS.required' => 'COUPON_STATUS обязателен для заполнения',
            '*.COUPON_STATUS.numeric' => 'Поле ответа должно быть числом',
        ];
    }
}
