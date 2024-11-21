<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AtelierResource extends JsonResource
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
            'name' => $this->name,
            'room' => $this->room,
            'equipments' => EquipmentResource::collection($this->whenLoaded('equipments')),
            'manager' => new UserResource($this->whenLoaded('manager')),
            'manager_id' => $this->manager_id,
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
