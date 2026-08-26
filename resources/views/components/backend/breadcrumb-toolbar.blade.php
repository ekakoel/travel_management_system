@props([
    'items' => [],
    'current' => null,
    'class' => '',
])

<section {{ $attributes->class(['backend-page-toolbar', 'backend-breadcrumb-toolbar', $class]) }}>
    <nav class="backend-breadcrumb-nav" aria-label="Breadcrumb">
        <ol class="breadcrumb backend-breadcrumb">
            @foreach ($items as $item)
                @php
                    $label = $item['label'] ?? null;
                    $url = $item['url'] ?? null;
                    $isActive = (bool) ($item['active'] ?? false);
                @endphp

                @continue(blank($label))

                <li class="breadcrumb-item{{ $isActive || blank($url) ? ' active' : '' }}" @if ($isActive || blank($url)) aria-current="page" @endif>
                    @if ($url && ! $isActive)
                        <a href="{{ $url }}">{{ $label }}</a>
                    @else
                        {{ $label }}
                    @endif
                </li>
            @endforeach

            @if (filled($current))
                <li class="breadcrumb-item active" aria-current="page">{{ $current }}</li>
            @endif
        </ol>
    </nav>

    @isset($actions)
        <div class="backend-page-toolbar__actions">
            {{ $actions }}
        </div>
    @endisset
</section>
