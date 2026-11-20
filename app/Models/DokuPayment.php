<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokuPayment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'Pending';
    public const STATUS_SUCCESS = 'Success';
    public const STATUS_FAILED = 'Failed';
    public const STATUS_EXPIRED = 'Expired';
    public const STATUS_REVIEW = 'Review';

    protected $fillable = [
        'order_id',
        'invoice_id',
        'user_id',
        'invoice_number',
        'request_id',
        'notification_request_id',
        'amount_idr',
        'credited_amount',
        'invoice_currency',
        'sell_rate',
        'status',
        'checkout_url',
        'expired_at',
        'paid_at',
        'original_request_id',
        'channel',
        'payment_confirmation_id',
        'review_reason',
        'raw_response',
        'raw_notification',
    ];

    protected $casts = [
        'amount_idr' => 'integer',
        'credited_amount' => 'decimal:2',
        'sell_rate' => 'decimal:8',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'raw_response' => 'array',
        'raw_notification' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceAdmin::class, 'invoice_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paymentConfirmation(): BelongsTo
    {
        return $this->belongsTo(PaymentConfirmation::class, 'payment_confirmation_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
