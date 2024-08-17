<?php

namespace App\Models;

use App\Notifications\ProfileUpdatedNotification;
use App\Notifications\UserStatusChangedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'balance',
        'currency',
        'language',
        'gender',
        'marital_status',
        'date_of_birth',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    /**
     * Boot the model.
     *
     * Add event listeners for user status and profile updates.
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($user) {
            if ($user->isDirty('status')) {
                $user->notify(new UserStatusChangedNotification($user->status));
            }

            $profileFields = ['name', 'email', 'balance', 'currency', 'language', 'gender', 'marital_status', 'date_of_birth'];
            if ($user->isDirty($profileFields)) {
                $user->notify(new ProfileUpdatedNotification($user));
            }
        });
    }

    /**
     * Check if the user has an admin role.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Get the URL to the user's profile image.
     *
     * @return string
     */
// app/Models/User.php

    public function adminlte_image()
    {
        $profileMedia = $this->media()->where('file_type', 'like', 'image%')->first();

        return $profileMedia
            ? asset( $profileMedia->path)
            : 'https://example.com/default-profile.png'; // Default profile image if none is found
    }


    /**
     * Get all of the media for the user.
     *
     * @return MorphMany
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Add balance to the user's account.
     *
     * @param float $amount
     * @param string|null $currency
     * @return void
     */
    public function addBalance(float $amount, string $currency = null): void
    {
        $this->balance += $amount;

        if ($currency) {
            $this->currency = $currency;
        }

        $this->save();
    }
}
