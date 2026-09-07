<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class HomeSlider extends Model
{
    protected $fillable = [
        'title',
        'title_traditional',
        'title_simplified',
        'description',
        'description_traditional',
        'description_simplified',
        'button_text',
        'button_text_traditional',
        'button_text_simplified',
        'button_url',
        'image',
        'mobile_image',
        'sort_order',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function getLocalizedTitleAttribute(): ?string
    {
        return $this->getLocalizedValue('title');
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return $this->getLocalizedValue('description');
    }

    public function getLocalizedButtonTextAttribute(): ?string
    {
        return $this->getLocalizedValue('button_text');
    }

    protected function getLocalizedValue(string $field): ?string
    {
        $locale = App::getLocale();

        $localizedField = match ($locale) {
            'zh' => $field . '_traditional',
            'zh-CN' => $field . '_simplified',
            default => $field,
        };

        return $this->{$localizedField} ?: $this->{$field};
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) {
                $query
                    ->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query
                    ->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            });
    }
}