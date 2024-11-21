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
        return $this->belongsToMany(User::class, 'atelier_user')->withPivot('teacher');
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($atelier) {
            // Check if manager_id was changed
            if ($atelier->isDirty('manager_id')) {
                $originalManagerId = $atelier->getOriginal('manager_id');
                $newManagerId = $atelier->manager_id;

                // Revoke permission from the old manager
                if ($originalManagerId) {
                    $originalManager = User::find($originalManagerId);
                    if ($originalManager) {
                        $permissionName = "manage_atelier";
                        $originalManager->revokePermissionTo($permissionName);
                    }
                }

                // Assign permission to the new manager
                if ($newManagerId) {
                    $newManager = User::find($newManagerId);
                    if ($newManager) {
                        $permissionName = "manage_atelier";
                        Permission::firstOrCreate(['name' => $permissionName]); // Ensure the permission exists
                        $newManager->givePermissionTo($permissionName);
                    }
                }
            }
        });
    }
}
