<!-- nav_hasmenu.blade -->

<li class="nav-item pcoded-hasmenu ">
    <a href="#!" class="nav-link  {{$color??""}}"><span class="pcoded-micon">
            <i class="{{$icon}}"></i></span>
        <span class="pcoded-mtext">{{$caption}}</span>
        @php $badge=0;@endphp
        @foreach($submenu as $item)
            @php $badge+=$item->menu->badge;@endphp
        @endforeach
        @if($badge)
            <span class="pcoded-badge label label-danger">{{$badge}}</span>
        @endif
    </a>
    <ul class="pcoded-submenu">
        @foreach($submenu as $item)
            <li class=""><a
                    href="{{route($item->menu->route,isset($item->menu->query_string)?$item->menu->query_string:"")}}"
                    class="">{{$item->menu->caption}}
                    @if($item->menu->badge)
                        <span class="pcoded-badge label label-danger">{{$item->menu->badge}}</span>
                    @endif
                </a></li>
        @endforeach

    </ul>
</li>

<!-- /nav_hasmenu.blade -->
