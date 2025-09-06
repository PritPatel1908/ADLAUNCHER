<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'username',
        'employee_id',
        'name',
        'gender',
        'phone',
        'mobile',
        'date_of_birth',
        'date_of_joining',
        'email',
        'password',
        'is_admin',
        'is_client',
        'is_user',
        'is_login',
        'last_ip_address',
        'last_url',
        'last_login_at',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'date_of_birth' => 'date',
            'date_of_joining' => 'date',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_client' => 'boolean',
            'is_user' => 'boolean',
            'is_login' => 'boolean',
            'gender' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * Get the column preferences for the user.
     */
    public function showColumns(): HasMany
    {
        return $this->hasMany(ShowColumn::class);
    }

    /**
     * Get the roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * Get the companies associated with the user.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_companies');
    }

    /**
     * Get the locations associated with the user.
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'user_locations');
    }

    /**
     * Get the areas associated with the user.
     */
    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'user_areas');
    }

    /**
     * Generate username from first_name and last_name
     */
    public static function generateUsername($firstName, $lastName)
    {
        $baseUsername = strtolower($firstName . $lastName);
        $username = $baseUsername;
        $counter = 1;

        while (self::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute()
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }
}
