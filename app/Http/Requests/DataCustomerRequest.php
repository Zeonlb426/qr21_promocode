<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DataCustomerRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'firstName' => ['required', 'string', 'max:32'],
            'lastName' => ['required', 'string', 'max:32'],
            'birthDate' => ['required', 'date_format:Y-m-d', 'before:'.now()->subYears(18)->toDateString()],
            'city' => ['required', 'string', 'max:32'],
            'tradeNetworkId' => ['integer', 'min:0', 'nullable'],
            'productId' => ['integer', 'min:0', 'nullable'],
        ];
    }
    public function messages(): array
    {
        return [
            'email.required' => 'Почта обязательна для заполнения',
            'email.email' => 'Введите корректный адрес почты',
            'firstName.required' => 'Имя обязательно для заполнения',
            'firstName.string' => 'Поле имени должно быть строкой',
            'firstName.max' => 'Количество символов не должно привышать 32',
            'lastName.required' => 'Фамилия обязательна для заполнения',
            'lastName.string' => 'Поле Фамилии должно быть строкой',
            'lastName.max' => 'Количество символов не должно привышать 32',
            'birthDate.required' => 'Дата рождения обязательна для заполнения',
            'birthDate.date_format' => 'Дата рождения должна быть в формате гггг-мм-дд',
            'birthDate.before' => 'Вам должно быть 18+',
            'city.required' => 'Город обязателен для заполнения',
            'city.string' => 'Поле города должно быть строкой',
            'city.max' => 'Количество символов не должно привышать 32',
            'tradeNetworkId.integer' => 'Поле должно быть целым числом',
            'tradeNetworkId.min' => 'Минимальное значение должно быть не меньше 0',
            'productId.integer' => 'Поле должно быть целым числом',
            'productId.min' => 'Минимальное значение должно быть не меньше 0',
        ];
    }
}
