@foreach ($items as $item)
    @if ($item->hasChilds())
        @include('rinvex/menus::item.dropdown', compact('item', $specialSidebar))
    @else
        @include('rinvex/menus::item.item', compact('item'))
    @endif
@endforeach
