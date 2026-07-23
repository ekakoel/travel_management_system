<section {{ $attributes->merge(['class' => 'backend-detail-layout']) }}>
    <div class="backend-detail-main">
        {{ $main ?? $slot }}
    </div>

    @isset($side)
        <aside class="backend-detail-side" aria-label="Detail context panel">
            {{ $side }}
        </aside>
    @endisset
</section>
