<?php

namespace App\Models;

use App\Notifications\ProfileUpdatedNotification;
use App\Notifications\TicketNotification;
use App\Notifications\TransactionNotification;
use App\Notifications\UserStatusChangedNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
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
        'referred_by', // Add referred_by here

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
            $profileFields = ['name', 'email'];
            if ($user->isDirty($profileFields)) {
                $user->notify(new ProfileUpdatedNotification($user));
            }
        });

        static::creating(function ($user) {
            // Generate a unique referral code with a prefix and random alphanumeric string
            $user->referral_code = strtoupper('REF-' . Str::random(6));

            // Ensure the referral code is unique in the database
            while (User::where('referral_code', $user->referral_code)->exists()) {
                $user->referral_code = strtoupper('REF-' . Str::random(6));
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
     * Get all transactions for the user.
     *
     * @return HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function createTransactionAndNotify($transactionData): Model
    {
        $transaction = $this->transactions()->create($transactionData);

        // If the transaction is a credit, the amount is $20 or more, and the user is not already active
        if ($transaction->type === 'credit' && $transaction->amount >= 20 && $this->status !== 'active') {
            $this->status = 'active';
            $this->save();

            // Notify the user that their status has changed
            $this->notify(new UserStatusChangedNotification($this->status));
        }

        $this->notify(new TransactionNotification($transaction));

        return $transaction;
    }


    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }


    public function createTicket($ticketData)
    {
        $ticket = $this->tickets()->create($ticketData);

        // Send notification after creating the ticket
        $this->notify(new TicketNotification($ticket));

        return $ticket;
    }

    /**
     * Get the URL to the user's profile image.
     *
     * @return string
     */
    public function adminlte_image()
    {
        $profileMedia = $this->media()->where('file_type', 'like', 'image%')->first();

        return $profileMedia
            ? asset($profileMedia->path)
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

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Get the users referred by this user.
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function canRequestBonus()
    {
        $referrals = $this->referrals()->get();
        foreach ($referrals as $referral) {
            Log::info("Referral User ID: {$referral->id}, Verified: {$referral->email_verified_at}, Balance: {$referral->balance}");
        }

        $qualifiedReferralsCount = $this->referrals()
            ->whereNotNull('email_verified_at')
            ->where('balance', '>=', 20)
            ->count();

        Log::info("User {$this->id} has {$qualifiedReferralsCount} qualified referrals.");

        return $qualifiedReferralsCount >= 50;
    }


    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'bg-success';  // Green badge for active
            case 'inactive':
                return 'bg-secondary'; // Gray badge for inactive
            case 'suspended':
                return 'bg-warning';  // Yellow badge for suspended
            case 'banned':
                return 'bg-danger';  // Red badge for banned
            default:
                return 'bg-secondary'; // Default color
        }
    }


}
