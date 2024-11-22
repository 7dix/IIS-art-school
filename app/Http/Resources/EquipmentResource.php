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
            'date_of_purchase' => $this->date_of_purchase,
            'maximum_leasing_period' => $this->maximum_leasing_period,
            'allowed_leasing_hours' => json_decode($this->allowed_leasing_hours),
            'owner' => new UserResource($this->owner),
            'type' => new TypeResource($this->type),
            'type_id' => $this->type_id,
            'atelier_id' => $this->atelier_id,
            'type_name' => $this->type->name,
            'atelier_name' => $this->atelier->name,
            'atelier' => new AtelierResource($this->atelier),
            'can_be_borrowed' => $this->can_be_borrowed,
            'restrictions' => $this->restrictions,
        ];
    }
}
