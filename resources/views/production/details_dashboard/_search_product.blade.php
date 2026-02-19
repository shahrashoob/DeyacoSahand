<div class="dropdown drp-user show">


    <a href="#" class="dropdown-toggle {{$product_equal_to!=""?"text-danger":""}}" data-toggle="dropdown" aria-expanded="true">
        نام کالا


    </a>
    <div class="dropdown-menu dropdown-menu-right profile-notification " style="padding: 10px">
        <form action="{{route($route)}}" method="post">
            @csrf
            @include("production.details_dashboard._hidden_inputs")
            <div class="row">
                <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
                    نام کالا برابر با:
                    <input type="text" name="product_equal_to" style="width: 95px; height: 20px" value="{{$product_equal_to}}">
                    <br/>
                    <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو</button>
                </div>
            </div>
        </form>

    </div>
    <div class="dropdown drp-user show" style="display: inline">
        (
        <a href="#" class="dropdown-toggle {{$search_consumed_product!=""?"text-danger":""}}"  data-toggle="dropdown" aria-expanded="true">
            مواد مصرفی
           </a>
        )
        <br/>
        <div class="dropdown-menu dropdown-menu-right profile-notification " style="padding: 10px">
            <form action="{{route($route)}}" method="post">
                @csrf
                @include("production.details_dashboard._hidden_inputs")
                <div class="row">
                    <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
                        نام مواد مصرفی برابر با:
                        <input type="text" name="search_consumed_product" style="width: 95px; height: 20px" value="{{$search_consumed_product}}">
                        <br/>
                        <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو</button>
                    </div>
                </div>
            </form>

        </div>

    </div>



</div>
<div class="dropdown drp-user show">


    <a href="#" class="dropdown-toggle {{$search_production_channel_type!=""?"text-danger":""}}"  data-toggle="dropdown" aria-expanded="true">

اولین کانال تولید

    </a>
    <br/>
    <div class="dropdown-menu dropdown-menu-right profile-notification " style="padding: 10px">
        <form action="{{route($route)}}" method="post">
            @csrf
            @include("production.details_dashboard._hidden_inputs")
            <div class="row">
                <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
                    نام کانال تولید برابر با:
                    <input type="text" name="search_production_channel_type" style="width: 95px; height: 20px" value="{{$search_production_channel_type}}">
                    <br/>
                    <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو</button>
                </div>
            </div>
        </form>

    </div>
</div>



