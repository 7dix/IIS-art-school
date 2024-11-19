<?php

namespace App\Http\Resources;

use App\Http\Resources\TypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class EquipmentResource extends JsonResource
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
            'year_of_manufacture' => $this->year_of_manufacture,
            'owner' => new UserResource($this->owner),
            'type' => new TypeResource($this->type),
        ];
    }
}
