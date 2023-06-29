@if ($item->isDivider())
    <li class="divider"></li>
@elseif ($item->isHeader())
    <li class="dropdown-header">{{ $item->title }}</li>
@else
    <li {{ $item->getItemAttributes() }}>
        <a href="{{ $item->getUrl() }}" {!! $item->getLinkAttributes() !!}>
            @if ($item->icon)<i class="{{ $item->icon }}"></i>@endif
            {{ $item->title }}
        </a>
    </li>
@endif
