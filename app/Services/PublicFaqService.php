<?php

namespace App\Services;

use App\Models\TermAndCondition;

class PublicFaqService
{
    public function groups()
    {
        $items = $this->activeFaqItems();

        if ($items->isEmpty()) {
            $items = $this->defaultFaqItems();
        }

        return collect([
            [
                'type'=>'FAQ',
                'title'=>__('messages.FAQs'),
                'items'=>$items,
            ],
        ]);
    }

    public function items()
    {
        return $this->groups()
            ->first()['items']
            ->map(fn ($item) => [
                'question'=>$item['title'],
                'answer'=>$item['content'],
            ])
            ->values();
    }

    private function activeFaqItems()
    {
        return TermAndCondition::where('type', 'FAQ')
            ->where('status', 'Active')
            ->get()
            ->map(function ($policy) {
                return [
                    'title'=>$this->localizedPolicyValue($policy, 'name'),
                    'content'=>$this->localizedPolicyValue($policy, 'policy'),
                ];
            })
            ->filter(fn ($policy) => filled($policy['title']) || filled($policy['content']))
            ->values();
    }

    private function localizedPolicyValue(TermAndCondition $policy, string $field): ?string
    {
        $locale = app()->getLocale();
        $suffix = str_starts_with($locale, 'zh') ? 'zh' : ($locale === 'en' ? 'en' : 'id');
        $localizedField = "{$field}_{$suffix}";
        $fallbackField = "{$field}_en";

        return $policy->{$localizedField} ?: $policy->{$fallbackField} ?: $policy->{"{$field}_id"};
    }

    private function defaultFaqItems()
    {
        return collect([
            [
                'title'=>__('home.faq.items.register.question'),
                'content'=>'<p>'.e(__('home.faq.items.register.answer')).'</p>',
            ],
            [
                'title'=>__('home.faq.items.partner.question'),
                'content'=>'<p>'.e(__('home.faq.items.partner.answer')).'</p>',
            ],
            [
                'title'=>__('home.faq.items.approval.question'),
                'content'=>'<p>'.e(__('home.faq.items.approval.answer')).'</p>',
            ],
            [
                'title'=>__('home.faq.items.promotions.question'),
                'content'=>'<p>'.e(__('home.faq.items.promotions.answer')).'</p>',
            ],
            [
                'title'=>__('home.faq.items.support.question'),
                'content'=>'<p>'.e(__('home.faq.items.support.answer')).'</p>',
            ],
            [
                'title'=>__('home.faq.items.history.question'),
                'content'=>'<p>'.e(__('home.faq.items.history.answer')).'</p>',
            ],
        ]);
    }
}
