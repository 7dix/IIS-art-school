<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function ateliers()
    {
        return $this->belongsToMany(Atelier::class);
    }
}
