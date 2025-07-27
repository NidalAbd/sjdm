<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'logo',
        'is_active',
        'min_amount',
        'max_amount',
        'processing_fee_fixed',
        'processing_fee_percentage',
        'currency',
        'processing_time_min',
        'processing_time_max',
        'supported_countries',
        'api_credentials',
        'gateway_url',
        'webhook_url',
        'instructions',
        'sort_order',
        'requires_verification',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'processing_fee_fixed' => 'decimal:2',
        'processing_fee_percentage' => 'decimal:2',
        'processing_time_min' => 'integer',
        'processing_time_max' => 'integer',
        'sort_order' => 'integer',
        'requires_verification' => 'boolean',
        'supported_countries' => 'array',
        'api_credentials' => 'array',
        'settings' => 'array',
    ];

    // Available payment types
    const PAYMENT_TYPES = [
        'credit_card' => 'Credit/Debit Card',
        'bank_transfer' => 'Bank Transfer',
        'cryptocurrency' => 'Cryptocurrency',
        'digital_wallet' => 'Digital Wallet',
        'mobile_payment' => 'Mobile Payment',
        'prepaid_card' => 'Prepaid Card',
        'cash' => 'Cash Payment',
        'other' => 'Other',
    ];

    // Popular currencies
    const CURRENCIES = [
        'USD' => 'US Dollar',
        'EUR' => 'Euro',
        'GBP' => 'British Pound',
        'AED' => 'UAE Dirham',
        'SAR' => 'Saudi Riyal',
        'EGP' => 'Egyptian Pound',
        'BTC' => 'Bitcoin',
        'ETH' => 'Ethereum',
        'USDT' => 'Tether',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Get transactions using this payment method
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'payment_method_id');
    }

    /**
     * Scope for active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for payment methods ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Check if payment method supports a specific country
     */
    public function supportsCountry($countryCode)
    {
        if (empty($this->supported_countries)) {
            return true; // No restrictions
        }
        
        return in_array($countryCode, $this->supported_countries);
    }

    /**
     * Calculate total fee for an amount
     */
    public function calculateFee($amount)
    {
        $percentageFee = ($amount * $this->processing_fee_percentage) / 100;
        return $this->processing_fee_fixed + $percentageFee;
    }

    /**
     * Calculate total amount including fees
     */
    public function calculateTotalAmount($amount)
    {
        return $amount + $this->calculateFee($amount);
    }

    /**
     * Check if amount is within limits
     */
    public function isAmountValid($amount)
    {
        if ($amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }

    /**
     * Get processing time range as formatted string
     */
    public function getProcessingTimeAttribute()
    {
        if ($this->processing_time_min === $this->processing_time_max) {
            return $this->processing_time_min . ' minutes';
        }
        
        return $this->processing_time_min . '-' . $this->processing_time_max . ' minutes';
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/app/public/' . $this->logo);
        }
        
        return asset('images/payment-methods/default.svg');
    }

    /**
     * Get payment type label
     */
    public function getTypeLabel()
    {
        return self::PAYMENT_TYPES[$this->type] ?? $this->type;
    }

    /**
     * Get currency label
     */
    public function getCurrencyLabel()
    {
        return self::CURRENCIES[$this->currency] ?? $this->currency;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        return $this->is_active ? 'badge-success' : 'badge-danger';
    }

    /**
     * Get status text
     */
    public function getStatusText()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
} 