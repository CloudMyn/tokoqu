<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    protected $with = [
        'phone_number', 'owner_store'
    ];

    public function phone_number()
    {
        return $this->belongsTo(PhoneNumber::class, 'phone_id');
    }

    public function owner_store()
    {
        return $this->hasOne(Owner::class, 'user_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function has_role($role): bool
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        } else {
            return $this->role == $role;
        }

        return false;
    }

    public function get_role_label(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'store_owner' => 'Pemilik Toko',
            'employee' => 'Karyawan Toko',
        };
    }
}
