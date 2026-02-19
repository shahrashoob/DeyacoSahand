<nav class="navbar m-b-30 p-10">
    <ul class="nav">
        <li class="nav-item f-text active">
            <a class="nav-link text-secondary" href="#">دوره ارزیابی: <span >
                    {{$workbook->project->caption}}
                </span></a>
        </li>
        <li class="nav-item f-text active">
            <a class="nav-link text-secondary" href="#">شروع دوره: <span >
                  {{$workbook->project->persain_start_date()}}

                </span></a>
        </li>
        <li class="nav-item f-text active">
            <a class="nav-link text-secondary" href="#">پایان دوره: <span >
                {{$workbook->project->persain_end_date()}}

                </span></a>
        </li>

    </ul>
    <div class="nav-item nav-grid f-view">

        <button type="button" class="btn btn-primary btn-icon m-0" data-toggle="tooltip" data-placement="top" title="">
            <i class="fas fa-th-large"></i>
        </button>
    </div>
</nav>


{{--<nav class="navbar m-b-30 p-10">--}}
{{--    <ul class="nav">--}}
{{--        <li class="nav-item f-text active">--}}
{{--            <a class="nav-link text-secondary" href="#">Filter: <span>(current)</span></a>--}}
{{--        </li>--}}
{{--        <li class="nav-item dropdown">--}}
{{--            <a class="nav-link dropdown-toggle text-secondary" href="#" id="bydate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="far fa-clock"></i> By Date</a>--}}
{{--            <div class="dropdown-menu" aria-labelledby="bydate">--}}
{{--                <a class="dropdown-item" href="#">Show all</a>--}}
{{--                <div class="dropdown-divider"></div>--}}
{{--                <a class="dropdown-item" href="#">Today</a>--}}
{{--                <a class="dropdown-item" href="#">Yesterday</a>--}}
{{--                <a class="dropdown-item" href="#">This week</a>--}}
{{--                <a class="dropdown-item" href="#">This month</a>--}}
{{--                <a class="dropdown-item" href="#">This year</a>--}}
{{--            </div>--}}
{{--        </li>--}}
{{--        <li class="nav-item dropdown">--}}
{{--            <a class="nav-link dropdown-toggle text-secondary" href="#" id="bystatus" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-chart-line"></i> By Status</a>--}}
{{--            <div class="dropdown-menu" aria-labelledby="bystatus">--}}
{{--                <a class="dropdown-item" href="#">Show all</a>--}}
{{--                <div class="dropdown-divider"></div>--}}
{{--                <a class="dropdown-item" href="#">Open</a>--}}
{{--                <a class="dropdown-item" href="#">On hold</a>--}}
{{--                <a class="dropdown-item" href="#">Resolved</a>--}}
{{--                <a class="dropdown-item" href="#">Closed</a>--}}
{{--                <a class="dropdown-item" href="#">Dublicate</a>--}}
{{--                <a class="dropdown-item" href="#">Wontfix</a>--}}
{{--            </div>--}}
{{--        </li>--}}
{{--        <li class="nav-item dropdown">--}}
{{--            <a class="nav-link dropdown-toggle text-secondary" href="#" id="bypriority" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-list-ol"></i> By Priority</a>--}}
{{--            <div class="dropdown-menu" aria-labelledby="bypriority">--}}
{{--                <a class="dropdown-item" href="#">Show all</a>--}}
{{--                <div class="dropdown-divider"></div>--}}
{{--                <a class="dropdown-item" href="#">Highest</a>--}}
{{--                <a class="dropdown-item" href="#">High</a>--}}
{{--                <a class="dropdown-item" href="#">Normal</a>--}}
{{--                <a class="dropdown-item" href="#">Low</a>--}}
{{--            </div>--}}
{{--        </li>--}}
{{--    </ul>--}}
{{--    <div class="nav-item nav-grid f-view">--}}
{{--        <span class="m-r-15">View Mode: </span>--}}
{{--        <button type="button" class="btn btn-primary btn-icon m-0" data-toggle="tooltip" data-placement="top" title="list view">--}}
{{--            <i class="fas fa-list-ul"></i>--}}
{{--        </button>--}}
{{--        <button type="button" class="btn btn-primary btn-icon m-0" data-toggle="tooltip" data-placement="top" title="grid view">--}}
{{--            <i class="fas fa-th-large"></i>--}}
{{--        </button>--}}
{{--    </div>--}}
{{--</nav>--}}
