<!-- nav_hasmenu.blade -->

<li class="nav-item pcoded-hasmenu ">
    <a href="#!" class="nav-link {{$color??""}}"><span class="pcoded-micon">
            <i class="{{$icon}}" ></i></span>
        <span class="pcoded-mtext">{{$caption}}</span>
    </a>
    <ul class="pcoded-submenu">
        @foreach($submenu as $item)
        <li class=""><a href="{{url($item["link"])}}" class="">{{$item["caption"]}}</a></li>
            @endforeach

    </ul>
</li>

<!-- /nav_hasmenu.blade -->
