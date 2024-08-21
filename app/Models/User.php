<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasAvatar
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
        'avatar_url',
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
        'phone_number',
        'owner_store'
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return cek_admin_role();
        } else if ($panel->getId() === 'store') {
            return cek_store_role();
        }

        return false;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url("$this->avatar_url") : null;
    }

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
            'store_employee' => 'Karyawan Toko',
        };
    }
}
