@if($specialSidebar)
    <li class="dropdown-header">@if($item->icon)<i class="{{ $item->icon }}"></i>@endif {{ $item->title }}</li>
    @foreach ($item->childs as $child)
        @if ($child->hasChilds())
            @include('rinvex/menus::child.dropdown', ['item' => $child])
        @else
            @include('rinvex/menus::item.item', ['item' => $child])
        @endif
    @endforeach
@else
    <li class="dropdown {{ $item->hasActiveOnChild() ? 'active' : '' }}">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            {{ $item->title }}
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" role="menu">
            @foreach ($item->childs as $child)
                @if ($child->hasChilds())
                    @include('rinvex/menus::child.dropdown', ['item' => $child])
                @else
                    @include('rinvex/menus::item.item', ['item' => $child])
                @endif
            @endforeach
        </ul>
    </li>
@endif
