<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultantBookingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge($this->resource->toFrontendArray(), [
            'commission_amount' => $this->commission_amount,
            'consultant_amount' => $this->consultant_amount,
            'client' => [
                'name' => $this->client->name,
                'email' => $this->client->email,
            ],
        ]);
    }
}
