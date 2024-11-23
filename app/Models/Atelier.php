<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use App\Models\User;


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
        return $this->hasMany(Equipment::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'atelier_user')->using(AtelierUser::class)->withPivot('teacher');
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($atelier) {
            if ($atelier->isDirty('manager_id')) {
                $originalManagerId = $atelier->getOriginal('manager_id');
                $newManagerId = $atelier->manager_id;

                if ($originalManagerId) {
                    $originalManager = User::find($originalManagerId);
                    if ($originalManager) {
                       
                        $originalManager->revokePermissionTo('manage_type');
                        $originalManager->revokePermissionTo('assign_teacher');
                        $originalManager->revokePermissionTo('assign_students');
                        $originalManager->revokePermissionTo('restrict_equipment');
                    }
                }

                if ($newManagerId) {
                    $newManager = User::find($newManagerId);
                    if ($newManager) {
                        
                        $newManager->givePermissionTo('assign_students');
                        $newManager->givePermissionTo('manage_type');
                        $newManager->givePermissionTo('assign_teacher');
                        $newManager->givePermissionTo('restrict_equipment');

                    }
                }
            }
        });

        static::created(function ($atelier) {
            
            if ($atelier->isDirty('manager_id')) {
                $newManagerId = $atelier->manager_id;
                if ($newManagerId) {
                    $newManager = User::find($newManagerId);
                    if ($newManager) {
                        

                        $newManager->givePermissionTo('manage_type');
                        $newManager->givePermissionTo('assign_teacher');
                        $newManager->givePermissionTo('assign_students');
                        $newManager->givePermissionTo('restrict_equipment');

                    }
                }
            }
        });


    }

}

class AtelierUser extends \Illuminate\Database\Eloquent\Relations\Pivot
{
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($pivot) {
            if ($pivot->isDirty('teacher')) {
                $user = User::find($pivot->user_id);

                if ($pivot->teacher) {
                    
                    $user->givePermissionTo('manage_equipment');
                    $user->givePermissionTo('restrict_equipment');
                    $user->givePermissionTo('assign_students');
                    $user->givePermissionTo('manage_reservation');
                } 
            }
        });

        static::created(function ($pivot) {
            if ($pivot->teacher) {
                $user = User::find($pivot->user_id);
                
                $user->givePermissionTo('manage_equipment');
                $user->givePermissionTo('restrict_equipment');
                $user->givePermissionTo('assign_students');
                $user->givePermissionTo('manage_reservation');
            }
        });
    }
}
