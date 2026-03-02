<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientBookingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge($this->resource->toFrontendArray(), [
            'calendly_event_uuid' => $this->calendly_event_uuid,
            'commission_amount' => $this->commission_amount,
            'consultant_amount' => $this->consultant_amount,
            'client_notes' => $this->client_notes,
            'cancellation_reason' => $this->cancellation_reason,
            'is_refund_eligible' => $this->isRefundEligible(),
            'has_review' => $this->review !== null,
            'consultant' => $this->consultantProfile ? [
                'name' => $this->consultantProfile->user->name,
                'slug' => $this->consultantProfile->slug,
                'avatar' => $this->consultantProfile->avatar,
            ] : null,
        ]);
    }
}
