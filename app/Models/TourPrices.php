<?php

namespace App\Models;

use App\Models\Tours;
use App\Support\CanonicalDecimalInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourPrices extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_READY = 'ready';
    public const STATUS_UNRESOLVED = 'unresolved';
    public const STATUS_INACTIVE = 'inactive';

    public const MARKUP_TYPE_PERCENTAGE = 'percentage';
    public const MARKUP_TYPE_USD = 'usd';
    public const MARKUP_TYPE_IDR = 'idr';

    public const MARKUP_TYPES = [
        self::MARKUP_TYPE_PERCENTAGE,
        self::MARKUP_TYPE_USD,
        self::MARKUP_TYPE_IDR,
    ];

    public const PRICING_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_READY,
        self::STATUS_UNRESOLVED,
        self::STATUS_INACTIVE,
    ];

    public static function allowedTransitionsFrom(?string $status): array
    {
        return match ($status) {
            self::STATUS_DRAFT => [
                self::STATUS_DRAFT,
                self::STATUS_READY,
                self::STATUS_UNRESOLVED,
            ],
            self::STATUS_READY => [
                self::STATUS_READY,
                self::STATUS_INACTIVE,
            ],
            self::STATUS_UNRESOLVED => [
                self::STATUS_UNRESOLVED,
                self::STATUS_DRAFT,
            ],
            self::STATUS_INACTIVE => [
                self::STATUS_INACTIVE,
                self::STATUS_READY,
            ],
            default => self::PRICING_STATUSES,
        };
    }

    protected $fillable=[
        'tour_id',
        'min_qty',
        'max_qty',
        'contract_rate',
        'contract_rate_idr',
        'markup',
        'markup_amount',
        'markup_type',
        'markup_currency',
        'markup_source',
        'markup_verified_at',
        'markup_verified_by',
        'pricing_data_status',
        'valid_from',
        'expired_date',
        'valid_until',
        'status',
    ];

    protected $casts = [
        'contract_rate_idr' => 'integer',
        'markup_verified_at' => 'immutable_datetime',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function setMarkupAmountAttribute(mixed $value): void
    {
        $this->attributes['markup_amount'] = CanonicalDecimalInput::normalize($value);
    }

    public function tours(){
        return $this->belongsTo(Tours::class,'tour_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'markup_verified_by');
    }

    public function scopeReadyForTravel($query, string $travelDate)
    {
        return $query
            ->where('contract_rate_idr', '>', 0)
            ->whereNotNull('markup_amount')
            ->where(function ($query) {
                $query->whereIn('markup_type', self::MARKUP_TYPES)
                    ->orWhere(function ($query) {
                        $query->whereNull('markup_type')
                            ->whereIn('markup_currency', ['USD', 'IDR']);
                    });
            })
            ->whereNotNull('markup_source')
            ->whereNotNull('markup_verified_at')
            ->whereNotNull('markup_verified_by')
            ->whereNotNull('valid_from')
            ->whereDate('valid_from', '<=', $travelDate)
            ->whereNotNull('valid_until')
            ->whereDate('valid_until', '>=', $travelDate);
    }

    public function resolvedMarkupType(): ?string
    {
        if (in_array($this->markup_type, self::MARKUP_TYPES, true)) {
            return $this->markup_type;
        }

        return match ($this->markup_currency) {
            'USD' => self::MARKUP_TYPE_USD,
            'IDR' => self::MARKUP_TYPE_IDR,
            default => null,
        };
    }

    public function hasCompleteConfiguration(): bool
    {
        $markup = (string) $this->markup_amount;
        $markupType = $this->resolvedMarkupType();
        $validMarkup = match ($markupType) {
            self::MARKUP_TYPE_PERCENTAGE => preg_match('/^\d+(?:\.\d+)?$/', $markup) === 1
                && (float) $markup <= 100,
            self::MARKUP_TYPE_USD => preg_match('/^\d+(?:\.\d{1,2}0*)?$/', $markup) === 1,
            self::MARKUP_TYPE_IDR => preg_match('/^\d+(?:\.0+)?$/', $markup) === 1,
            default => false,
        };

        return (int) $this->contract_rate_idr > 0
            && $validMarkup
            && ! blank($this->markup_source)
            && $this->markup_verified_at !== null
            && $this->markup_verified_by !== null
            && $this->valid_from !== null
            && $this->valid_until !== null
            && $this->valid_from->lte($this->valid_until)
            && (int) $this->min_qty >= 1
            && (int) $this->max_qty >= (int) $this->min_qty;
    }
}
