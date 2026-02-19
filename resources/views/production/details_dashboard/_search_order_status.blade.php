<div class="dropdown drp-user show">

    مقدار سفارش
    <a href="#" class="dropdown-toggle {{
    $order_status_equal_to?
     "text-danger":(isset($data_fixed["order_status_fixed"]) && count($data_fixed["order_status_fixed"])>0?"text-success":"")
     }}" data-toggle="dropdown" aria-expanded="true">


        <br/>
        وضعیت سفارش

    </a>
    <div class="dropdown-menu dropdown-menu-right profile-notification ">
        <form action="{{route($route)}}" method="post">
            @csrf
            @include("production.details_dashboard._hidden_inputs")
        <div class="row">
            <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
                برابر با:
                <select name="order_status_equal_to"  style="width: 95px; height: 20px">
                    @foreach($order_status_option["items"] as $option)
                        <option value="{{$option["value"]}}" {{isset($option["selected"])?"selected":""}}>{{$option["text"]}}</option>
                    @endforeach
                </select> <br/>
                <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو</button>
                <br/>
               <div style="text-align: right;padding: 10px " >
                   <span class="text-success text-bold">فیلتر دائمی</span>
                   <br/>
                   @foreach($order_status_option["items"] as $option)
                       <input type="checkbox" name="order_status_fixed[{{$option["value"]}}]"
                       {{isset($data_fixed["order_status_fixed"]) && in_array($option["value"],$data_fixed["order_status_fixed"])?"checked":""}}
                       > {{$option["text"]}} <br/>
                   @endforeach
                       &nbsp;

               </div>

            </div>
        </div>
        </form>

    </div>
</div>