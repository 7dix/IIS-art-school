<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'name',
        'type_id',
        'owner_id',
        'atelier_id',
        'year_of_manufacture',
        'date_of_purchase',
        // Maximální doba pronájmu (ve dnech)
        'maximum_leasing_period',
        // Hodiny ve kterých je možné zařízení pronajmout json array
        'allowed_leasing_hours',
        'image',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function atelier()
    {
        return $this->belongsTo(Atelier::class);
    }

    public function restrictions() {
        return $this->belongsToMany(User::class);

    }

    public function reservation() {
        return $this->hasMany(Reservation::class);
    }
    
}
