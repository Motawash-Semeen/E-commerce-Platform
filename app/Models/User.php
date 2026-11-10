<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_SHOP_OWNER = 'shop_owner';

    /**
     * role and is_active are left out on purpose: they control access, so they
     * are only ever set explicitly ($user->role = ...), never from form input.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Same defaults as the database columns, so a new User object (not yet
     * saved or re-read) already knows its role and status.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => self::ROLE_SHOP_OWNER,
        'is_active' => true,
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * A shop owner's shop (at most one). Owners' own pages always use
     * auth()->user()->shop, never a shop id from the request.
     */
    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }

    /**
     * Only shop owner accounts: User::shopOwners()->...
     */
    public function scopeShopOwners(Builder $query): void
    {
        $query->where('role', self::ROLE_SHOP_OWNER);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isShopOwner(): bool
    {
        return $this->role === self::ROLE_SHOP_OWNER;
    }

    /**
     * Where this user lands after logging in.
     */
    public function homeRoute(): string
    {
        return $this->isAdmin() ? route('admin.dashboard') : route('owner.dashboard');
    }
}
