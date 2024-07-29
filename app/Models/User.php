<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;
    use Authorizable;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
        'activated_at',
        'banned_at'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'roles_users');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeBanned(Builder $query): Builder
    {
        return $query->whereNotNull('banned_at');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActivated(Builder $query): Builder
    {
        return $query->whereNotNull('activated_at');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeNoAdmin(Builder $query): Builder
    {
        return $query->whereDoesntHave('roles', function ($query) {
            /** @var Builder $query */
            $query->whereNot('name', Role::ADMIN_ROLE_NAME);
        });
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeAdmin(Builder $query): Builder
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', Role::ADMIN_ROLE_NAME);
        });
    }
}
