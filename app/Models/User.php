<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable implements HasName, MustVerifyEmail, CanResetPassword
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'barangay_id',  
        'phone_number',
        'role_id', 
    ];

    
    protected $table = 'users';
    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        
    ];

    public function roles()
{
    return $this->belongsTo(Role::class, 'role_id');  
}


    public function pets()
    {
        return $this->hasMany(Pet::class, 'UserID'); 
    }

    

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }


    public function getFilamentName(): string
    {
        return trim("{$this->firstname} {$this->lastname}");
    }

    public function getUserName(Model | Authenticatable $user): string
    {
        if ($user instanceof HasName) {
            return $user->getFilamentName(); 
    }

    return trim("{$user->firstname} {$user->lastname}");
    }

    public function canAccessFilament(): bool
{
    return $this->role_id === 1;
}


    
}
