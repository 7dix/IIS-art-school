<?php

use App\Http\Controllers\AtelierController;
use App\Http\Controllers\MyReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TypeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ReservationController;
use App\Http\Middleware\EnsureUserHasRole;



Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');});
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('/profile', [ProfileController::class, 'changePassword'])->name('password.update');
    

    Route::get('/api/ateliers/{atelier}/users-in-atelier', [AtelierController::class, 'usersInAtelier']);    
    Route::delete('/ateliers/{atelier}/users/{user}', [AtelierController::class, 'removeUser']);    
    Route::get('/atelier/{id}/dashboard', [AtelierController::class, 'dashboard'])->name('ateliers.dashboard');
    Route::get('/api/ateliers/{atelier}/available-users', [AtelierController::class, 'availableUsers']);    
    Route::resource('atelier', AtelierController::class);
    Route::post('/ateliers/{atelier}/users', action: [AtelierController::class, 'addUsers']);
    Route::post('/ateliers/{atelier}/restrictions', action: [AtelierController::class, 'addRestrictions']);
    Route::post('/ateliers/{atelier}/remove-teacher-role', [AtelierController::class, 'removeTeacherRole']);
    Route::post('/ateliers/{atelier}/add-teachers', [AtelierController::class, 'addTeachers']);
    Route::post('/ateliers/{atelier}/remove-restrictions', [AtelierController::class, 'removeRestrictions']);

    Route::get('/my-reservation/create', [MyReservationController::class, 'create'])->name('my-reservation.create');
    Route::post('/my-reservation/prepare', [MyReservationController::class, 'prepare'])->name('my-reservation.prepare');
    Route::resource('my-reservation', MyReservationController::class);
    Route::get('my-reservation', [MyReservationController::class, 'index'])->name('my-reservation.index');
    
    Route::resource('reservation', ReservationController::class);
    Route::post('/reservation/{reservation}/state-update', [ReservationController::class, 'reservationStateUpdate'])->name('reservation.state-update');

    Route::resource('user', UserController::class);
    Route::resource('equipment', EquipmentController::class);

    Route::get('/types/create', [TypeController::class, 'create'])->name('types.create');
    Route::resource('types', TypeController::class);
    Route::get('/api/types/{type}/user-equipment', action: [MyReservationController::class, 'getUserEquipmentByType']);
    Route::get('/api/ateliers/{atelier}/equipment', [AtelierController::class, 'getEquipment']);
    Route::get('/api/ateliers/{atelier}/users/{user}/restricted-equipment', [AtelierController::class, 'getRestrictedEquipment']);
    Route::get('/api/equipment/{id}/reservations', [MyReservationController::class, 'getReservations']);
  

    Route::put('/api/user/{id}', [UserController::class, 'update']);
    Route::put('/api/type/{id}', [TypeController::class, 'update']);
    Route::put('/api/equipment/{id}', [EquipmentController::class, 'update']);
    Route::get('/api/get-equipment/{id}', [EquipmentController::class, 'getEquipmentById']);
    Route::put('/api/block-equipment/{id}/{value}', [EquipmentController::class, 'blockEquipment']);
    Route::put('/api/atelier/{id}', [AtelierController::class, 'update']);
    Route::post('/api/userDelete/{id}', [UserController::class, 'destroy'])->name('users.destroy');;
    Route::get('/api/getAteliersWithType/{id}', [AtelierController::class, 'getAteliersWithType']);
});

require __DIR__.'/auth.php';
