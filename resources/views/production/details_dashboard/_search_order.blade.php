<div class="dropdown drp-user show">


    <a href="#" class="dropdown-toggle {{$order_equal_to!=""?"text-danger":""}}" data-toggle="dropdown"
       aria-expanded="true">
        سفارش


    </a>
    <div class="dropdown-menu dropdown-menu-right profile-notification ">
        <form action="{{route($route)}}" method="post">
            @csrf
            @include("production.details_dashboard._hidden_inputs")
            <div class="row">
                <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
                    برابر با:
                    <input type="text" name="order_equal_to" style="width: 85px; height: 20px"
                           value="{{$order_equal_to}}">
                    <br/>
                    <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو</button>
                </div>
            </div>
        </form>

    </div>
</div>

@if($allow_show_customer_caption_in_production_dashboard)
    <div class="dropdown drp-user show">

        <a href="#" class="dropdown-toggle {{$order_customer_equal_to!=""?"text-danger":""}}" data-toggle="dropdown"
           aria-expanded="true">
            نام مشتری

        </a>
        <div class="dropdown-menu dropdown-menu-right profile-notification ">
            <form action="{{route($route)}}" method="post">
                @csrf
                @include("production.details_dashboard._hidden_inputs")
                <div class="row">
                    <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
                        برابر با:
                        <input type="text" name="order_customer_equal_to" style="width: 85px; height: 20px"
                               value="{{$order_customer_equal_to}}">
                        <br/>
                        <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو</button>
                    </div>
                </div>
            </form>

        </div>
@else
    <br/>
@endif
