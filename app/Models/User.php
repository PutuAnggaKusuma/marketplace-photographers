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
        'last_seen_at',
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
            'last_seen_at' => 'datetime',
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

    public function rolePhotographer()
    {
        return $this->hasOne(RolePhotographer::class, 'id_user', 'id');
    }

    /**
     * Get the user's avatar URL from their rolePhotographer foto.
     * Returns null if no foto is set (views should fall back to initials).
     *
     * STANDAR PROYEK (Rule #44): Mekanisme Avatar User
     * - Jika user memiliki foto profil (via role_photographers.foto) -> tampilkan foto
     * - Jika tidak ada foto -> tampilkan inisial nama (via $user->initials)
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $foto = $this->rolePhotographer?->foto;
        if (!$foto) return null;
        return str_starts_with($foto, 'http') ? $foto : asset('storage/' . $foto);
    }

    /**
     * Get user initials (max 2 uppercase letters) from nama.
     * Fallback when avatar_url is null.
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->nama ?? 'U'));
        return strtoupper(
            substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : '')
        );
    }
}