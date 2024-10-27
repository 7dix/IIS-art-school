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
            'types' => TypeResource::collection($this->whenLoaded('types')),  
            'equipments' => EquipmentResource::collection($this->whenLoaded('equipments')),
            'manager' => new UserResource($this->whenLoaded('manager')),
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
