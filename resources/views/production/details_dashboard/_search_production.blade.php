<div class="dropdown drp-user show">


    <a href="#" class="dropdown-toggle {{$production_equal_to!=""?"text-danger    ":""}}" data-toggle="dropdown" >
        سریال تولید



        &nbsp;
    </a>
    <div class="dropdown-menu dropdown-menu-right profile-notification " style="padding: 10px">
        <form action="{{route($route)}}" method="post">
            @csrf
            @include("production.details_dashboard._hidden_inputs")
            <div class="row">
                <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
                    سریال تولید برابر با:
                    <input type="text" name="production_equal_to" style="width: 95px; height: 20px" value="{{$production_equal_to}}">
                    <br/>
                    <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو</button>
                </div>
            </div>
        </form>

    </div>
</div>
    اولویت -
<div class="dropdown drp-user show" style="display: inline">
    @php $selected_p=false; @endphp

    @foreach($waiting_status_option["items"] as $option)
        @php $selected_p=(isset($option["selected"])?true:false)|| $selected_p; @endphp
    @endforeach

    <a href="#" class="dropdown-toggle {{$selected_p?"text-danger":""}}" data-toggle="dropdown"  >
        وضعیت

    </a>
    <div class="dropdown-menu dropdown-menu-right  ">
        <form action="{{route($route)}}" method="post">
            @csrf
            @include("production.details_dashboard._hidden_inputs")
            <div class="row">
                <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
                    برابر با:
                    <select name="waiting_status_id"  style="width: 95px; height: 20px">
                    @foreach($waiting_status_option["items"] as $option)
                        <option value="{{$option["value"]}}" {{isset($option["selected"])?"selected":""}}>{{$option["text"]}}</option>
                    @endforeach
                    </select>
{{--                    <input type="text"  value="{{$production_status_equal_to}}">--}}
                    <br/>
                    <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو</button>
                </div>
            </div>
        </form>

    </div>
</div>