<div class="dropdown drp-user show">


    <a href="#" class="dropdown-toggle {{$order_equal_to!=""?"text-danger":""}}" data-toggle="dropdown" aria-expanded="true">
       شماره سفارش


    </a>
    <div class="dropdown-menu dropdown-menu-right profile-notification " style="padding: 10px">
{{--        <form action="{{route($route, $order)}}" method="post">--}}
{{--            @csrf--}}

            <div class="row">
                <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
                    کد سفارش برابر با:
                    <input type="text" name="order_equal_to" id="order_equal_to" style="width: 95px; height: 20px" value="{{$order_equal_to}}">
                    <br/>
                    <button type="submit" class="btn btn-primary btn-sm btn_search " >جستجو</button>
                </div>
            </div>
{{--        </form>--}}

    </div>

</div>