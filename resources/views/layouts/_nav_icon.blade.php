
<li data-username="" class="nav-item">
    <a href="{{url($link)}}" class="nav-link">
        <span class="pcoded-micon"><i class=" {{$icon}}"></i></span>
        <span class="pcoded-mtext">{{$caption}}</span>

        @if(isset($badge) && $badge)
            <span class="pcoded-badge label label-danger">{{$badge}}</span>
        @endif
    </a>

</li>
