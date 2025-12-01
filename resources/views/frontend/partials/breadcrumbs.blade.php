<nav class="breadcrumb-nav text-center mt-1 mb-1">
    <ol class="breadcrumb-list">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (isset($breadcrumb['url']))
                <li>
                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                </li>
            @else
                <li class="active" aria-current="page">{{ $breadcrumb['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
