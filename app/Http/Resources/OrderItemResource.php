<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->id,
            'product_name' => $this->nome,
            'quantity' => $this->pivot->quantidade ?? 1,
            'price_at_purchase' => $this->pivot->valorCompra ?? $this->preco,
            'subtotal' => ($this->pivot->valorCompra ?? $this->preco) * ($this->pivot->quantidade ?? 1),
            'photo_url' => $this->photo_url,
        ];
    }
}

