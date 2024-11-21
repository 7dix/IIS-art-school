<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function ateliers()
    {
        return $this->belongsToMany(Atelier::class, 'atelier_user')->withPivot('teacher');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function restrictions() {
        return $this->belongsToMany(Equipment::class);
    }

    // Manager thingy
    public function isManager()
    {
        return Atelier::where('manager_id', $this->id)->exists();
    }
    public function isManagerOfAtelier($atelierId)
    {
        return Atelier::where('id', $atelierId)
            ->where('manager_id', $this->id)
            ->exists();
    }

    public function managedAteliers()
    {
        return Atelier::where('manager_id', $this->id)->get();
    }




    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }


}
