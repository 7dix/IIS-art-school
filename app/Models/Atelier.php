<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atelier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'room',
        'manager_id',
    ];
    
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }    

    public function equipments()
    {
        return $this->belongsToMany(Equipment::class);
    }

    public function types()
    {
        return $this->belongsToMany(Type::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function isManagerAlsoUser()
    {
        return $this->users->contains($this->manager_id);
    }
}
