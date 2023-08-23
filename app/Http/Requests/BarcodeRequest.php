<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarcodeRequest extends FormRequest
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
            'tradeNetworkId' => ['required', 'integer'],
            'productId' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'tradeNetworkId.required' => 'Поле id торговой сети обязательно для заполнения',
            'tradeNetworkId.integer' => 'Поле id торговой сети должно содержать только цифры',
            'productId.required' => 'Поле id продукта обязательно для заполнения',
            'productId.integer' => 'Поле id продукта должно содержать только цифры',
        ];
    }
}
