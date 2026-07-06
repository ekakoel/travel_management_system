@php
    $breadcrumbVariant = $variant ?? null;
    $breadcrumbClass = trim('breadcrumb frontend-breadcrumb ' . ($breadcrumbVariant === 'dark' ? 'frontend-breadcrumb--dark' : ''));
@endphp

<nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap">
    <ol class="{{ $breadcrumbClass }}">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (isset($breadcrumb['url']))
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                </li>
            @else
                <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
