<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
        if ($this->hasFile('foto')) {
            return [
                'nome' => 'required|string|max:255',
                'preco' => 'required|numeric|min:0',
                'foto' => 'required|image|mimes:jpeg,jpg,png|max:2048',
                'type' => 'nullable|string|max:255',
            ];
        }

        return [
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'foto' => 'required|string|url',
            'type' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'A foto é obrigatória.',
            'foto.image' => 'O arquivo deve ser uma imagem.',
            'foto.mimes' => 'A imagem deve ser JPEG, JPG ou PNG.',
            'foto.max' => 'A imagem não pode ter mais de 2MB.',
            'foto.url' => 'A URL da foto é inválida.',
        ];
    }
}
