<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|numeric|min:0.01',
            'stock' => 'sometimes|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'O nome do produto deve ser um texto',
            'name.max' => 'O nome do produto não pode ter mais de 255 caracteres',
            'description.string' => 'A descrição deve ser um texto',
            'price.numeric' => 'O preço deve ser um número',
            'price.min' => 'O preço deve ser maior que zero',
            'stock.integer' => 'A quantidade em estoque deve ser um número inteiro',
            'stock.min' => 'A quantidade em estoque não pode ser negativa',
        ];
    }
}
