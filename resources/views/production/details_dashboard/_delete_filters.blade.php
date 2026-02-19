<div class="dropdown drp-user show" style="display: inline-block">


    <a href="#" class=" text-danger dropdown-toggle {{$order_equal_to!=""?"text-danger":""}}" data-toggle="dropdown" aria-expanded="true">
       حذف فیلترها
        <i class="fa fa-trash"></i>

    </a>
    <div class="dropdown-menu dropdown-menu-right profile-notification ">
        <form action="{{route($route)}}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-12" style="text-align: right; font-size: 13px; font-weight: normal">
                    <input type="radio" name="delete_filter" value="1" checked>
                    حذف همه فیلتر های موقت

<br/>
                    <input type="radio" name="delete_filter" value="2">
                    حذف کل



                    <button type="submit" class="btn-danger btn-delete btn-sm btn_search">حذف</button>
                </div>
            </div>
        </form>

    </div>
</div>

