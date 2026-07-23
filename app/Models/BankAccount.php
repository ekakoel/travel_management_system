<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BankAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'bank',
        'currency',
        'name',
        'account_name',
        'account_number',
        'account_idr',
        'account_cny',
        'account_twd',
        'account_usd',
        'location',
        'address',
        'telephone',
        'swift_code',
        'bank_code',
    ];

    public function getAccountNameAttribute($value)
    {
        return $value ?: ($this->attributes['name'] ?? null);
    }

    public function setAccountNameAttribute($value): void
    {
        if ($this->hasAccountNameColumn()) {
            $this->attributes['account_name'] = $value;
            return;
        }

        $this->attributes['name'] = $value;
    }

    public function getAccountNumberAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $currency = strtolower((string) ($this->attributes['currency'] ?? ''));
        $legacyColumn = "account_{$currency}";

        return $this->attributes[$legacyColumn] ?? null;
    }

    public function setAccountNumberAttribute($value): void
    {
        if ($this->hasAccountNumberColumn()) {
            $this->attributes['account_number'] = $value;
            return;
        }

        $currency = strtolower((string) ($this->attributes['currency'] ?? ''));
        $legacyColumn = in_array($currency, ['idr', 'usd', 'cny', 'twd'], true)
            ? "account_{$currency}"
            : 'account_idr';

        $this->attributes[$legacyColumn] = $value;
    }

    private function hasAccountNameColumn(): bool
    {
        try {
            return Schema::hasColumn($this->getTable(), 'account_name');
        } catch (\Throwable $exception) {
            return array_key_exists('account_name', $this->attributes);
        }
    }

    private function hasAccountNumberColumn(): bool
    {
        try {
            return Schema::hasColumn($this->getTable(), 'account_number');
        } catch (\Throwable $exception) {
            return array_key_exists('account_number', $this->attributes);
        }
    }
}
