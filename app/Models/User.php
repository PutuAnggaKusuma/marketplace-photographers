<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'is_protected',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_protected' => 'boolean',
        ];
    }

    /**
     * Role Check Helpers
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isPhotographer(): bool
    {
        return $this->role === 'photographer';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isProtected(): bool
    {
        return $this->is_protected || $this->role === 'super_admin';
    }

    /**
     * Override delete method to strictly protect Super Admin accounts.
     */
    public function delete()
    {
        if ($this->isProtected()) {
            throw new \Exception("Akses Ditolak! Akun Super Admin bersifat permanen & protected, tidak dapat dihapus dari sistem.");
        }

        return parent::delete();
    }
}
