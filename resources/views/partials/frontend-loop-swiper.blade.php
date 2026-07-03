@php
    $loopSwiperItems = collect($items ?? []);
    $loopSwiperBaseCount = $loopSwiperItems->count();
    $loopSwiperRenderedItems = $loopSwiperItems;
    $minimumRenderedSlides = max((int) ($minimumRenderedSlides ?? 1), 1);
    $loopSwiperInitialSlide = 0;
    $swiperClasses = trim('swiper frontend-loop-swiper ' . ($swiperClass ?? ''));
    $wrapperClasses = trim('swiper-wrapper ' . ($wrapperClass ?? ''));
    $slideVariable = $slideVariable ?? 'item';
    $showNavigation = $showNavigation ?? true;
    $showPagination = $showPagination ?? false;
    $swiperSpeed = (int) ($swiperSpeed ?? 800);
    $swiperEffect = $swiperEffect ?? 'coverflow';

    if ($loopSwiperBaseCount > 0 && $loopSwiperBaseCount < $minimumRenderedSlides) {
        $repeatCount = (int) ceil($minimumRenderedSlides / $loopSwiperBaseCount);

        if ($repeatCount % 2 === 0) {
            $repeatCount++;
        }

        for ($repeatIndex = 1; $repeatIndex < $repeatCount; $repeatIndex++) {
            $loopSwiperRenderedItems = $loopSwiperRenderedItems->concat($loopSwiperItems);
        }
    }

    if ($loopSwiperRenderedItems->count() > 1) {
        $loopSwiperInitialSlide = (int) floor($loopSwiperRenderedItems->count() / 2);
    }
@endphp

@if ($slidePartial && $loopSwiperRenderedItems->isNotEmpty())
    <div
        class="{{ $swiperClasses }}"
        data-swiper-base-count="{{ $loopSwiperBaseCount }}"
        data-swiper-slide-count="{{ $loopSwiperRenderedItems->count() }}"
        data-swiper-initial-slide="{{ $loopSwiperInitialSlide }}"
        data-swiper-speed="{{ $swiperSpeed }}"
        data-swiper-effect="{{ $swiperEffect }}"
    >
        <div class="{{ $wrapperClasses }}" @if (!empty($wrapperDelay)) data-wow-delay="{{ $wrapperDelay }}" @endif>
            @foreach ($loopSwiperRenderedItems as $swiperIndex => $loopSwiperItem)
                @include($slidePartial, array_merge($slideContext ?? [], [
                    $slideVariable => $loopSwiperItem,
                    'swiperIndex' => $swiperIndex,
                ]))
            @endforeach
        </div>

        @if ($showPagination)
            <div class="swiper-pagination"></div>
        @endif

        @if ($showNavigation)
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        @endif
    </div>
@elseif (!empty($emptyMessage))
    <p class="{{ $emptyMessageClass ?? 'text-center' }}">{{ $emptyMessage }}</p>
@endif
