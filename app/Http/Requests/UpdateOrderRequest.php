<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::in(['created', 'confirmed', 'canceled', 'delivered'])],
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'O status deve ser: created, confirmed, canceled ou delivered.',
            'items.min' => 'O pedido deve ter pelo menos um item.',
            'items.*.product_id.exists' => 'O produto selecionado não existe.',
            'items.*.quantity.min' => 'A quantidade deve ser maior que zero.',
        ];
    }
}
