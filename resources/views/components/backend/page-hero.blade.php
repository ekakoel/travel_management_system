@props([
    'eyebrow' => null,
    'title' => null,
    'description' => null,
    'class' => '',
])

<section {{ $attributes->class(['backend-page-hero', $class]) }}>
    <div>
        @isset($kicker)
            <span class="backend-page-eyebrow">{{ $kicker }}</span>
        @elseif ($eyebrow)
            <span class="backend-page-eyebrow">{{ $eyebrow }}</span>
        @endisset

        <h1>
            @isset($heading)
                {{ $heading }}
            @else
                {{ $title }}
            @endisset
        </h1>

        @isset($copy)
            {{ $copy }}
        @elseif ($description)
            <p>{{ $description }}</p>
        @endisset
    </div>

    @isset($action)
        {{ $action }}
    @endisset
</section>
