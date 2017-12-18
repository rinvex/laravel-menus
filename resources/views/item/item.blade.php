@if ($item->isDivider())
    <li class="divider"></li>
@elseif ($item->isHeader())
    <li class="dropdown-header">{{ $item->title }}</li>
@else
    <li class="{{ $item->isActive() ? 'active' : '' }}">
        <a href="{{ $item->getUrl() }}" {!! $item->getAttributes() !!}>
            @if ($item->icon)<i class="{{ $this->icon }}"></i>@endif
            {{ $item->title }}
        </a>
    </li>
@endif
