<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é obrigatório',
            'name.string' => 'O nome do produto deve ser um texto',
            'name.max' => 'O nome do produto não pode ter mais de 255 caracteres',
            'description.string' => 'A descrição deve ser um texto',
            'price.required' => 'O preço é obrigatório',
            'price.numeric' => 'O preço deve ser um número',
            'price.min' => 'O preço deve ser maior que zero',
            'stock.required' => 'A quantidade em estoque é obrigatória',
            'stock.integer' => 'A quantidade em estoque deve ser um número inteiro',
            'stock.min' => 'A quantidade em estoque não pode ser negativa',
        ];
    }
}
