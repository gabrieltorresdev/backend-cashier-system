<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'activated',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    public function accessPermission()
    {
        return $this->belongsTo(AccessPermission::class);
    }

    public function cashRegisters()
    {
        return $this->hasMany(CashRegister::class);
    }

    /**
     * Finds the user by the given email or username
     * @param string $emailOrUsername
     * @return Collection
     */
    public static function findByEmailOrUsername(string $emailOrUsername): Collection
    {
        return collect(
            self::where('email', '=', $emailOrUsername)
                ->orWhere('username', '=', $emailOrUsername)
                ->first()
                ?->toArray()
        );
    }

    public function getOpenedCashRegister(): Collection
    {
        $return = collect(
            $this->cashRegisters
                ->where('opened', '=', true)
                ->last()
                ?->toArray()
        );

        return $return->only([
            'id',
            'date_time',
            'initial_balance',
            'current_balance'
        ]);
    }
}
