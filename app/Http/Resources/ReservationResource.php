<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'equipments' => EquipmentResource::collection($this->whenLoaded('equipments')),
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'user' => UserResource::collection($this->whenLoaded('user')),
            'status' => $this->status,
        ];
    }
}
