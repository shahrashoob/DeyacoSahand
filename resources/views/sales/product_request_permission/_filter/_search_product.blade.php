<div class="dropdown drp-user show">


    <a href="#" class="dropdown-toggle {{$product_equal_to!=""?"text-danger":""}}" data-toggle="dropdown" aria-expanded="true">
        نام کالا


    </a>
    <div class="dropdown-menu dropdown-menu-right profile-notification " style="padding: 10px">
{{--        <form action="{{route($route,$order)}}" method="post">--}}
{{--            @csrf--}}
{{--            @include("sales.product_request_permission._filter._hidden_inputs")--}}
            <div class="row">
                <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
                    نام کالا برابر با:
                    <input type="text" id="product_equal_to" style="width: 95px; height: 20px" value="{{$product_equal_to}}">
                    <br/>
                    <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو</button>
                </div>
            </div>
{{--        </form>--}}

    </div>

</div>