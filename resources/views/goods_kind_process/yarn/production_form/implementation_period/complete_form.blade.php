@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  چله کشی  ")

@section('content')


    <form id="form1" autocomplete="off"
          action="{{route("yarn.production_form.implementation_period_form.submit_complete_form",$product)}}"
          method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ثبت فرم تولید نخ ( ویژه دوره پیاده سازی) </h5>
                    </div>
                    <div class="card-block">


                        <div class="row">
                            <div class="col-md-12">
                                <h4>{{$product->caption}}</h4>
                                @foreach($property as $item)
                                    <span class="col-2">
                                        {{$item->caption}}:
                                        <b>
                                            {{$property_value[$item->id]??""}} {{$item->special_unit->caption??""}}
                                        </b>
                                    </span>

                                @endforeach
                            </div>


                        </div>
                        <div class="row">
                            <br/>
                            <br/>
                            <div class="w-100"></div>
                            @include("component.input._number",["id"=>"amount",'label'=>"مقدار اصلی (".$product->unit->caption.")","value"=>$request->amount??"","autofocus"=>1])
                            @if(isset($product->sub_unit))
                                @include("component.input._number",["id"=>"sub_amount",'label'=>"مقدار فرعی (".$product->sub_unit->caption.")","value"=>$request->sub_amount??""])
                            @endif

                            @include("component.input._number",["id"=>"carrier_code",'label'=>"شماره حامل","value"=>$request->carrier_code??""])
                            @include("component.input._number",["id"=>"lot_number",'label'=>"شماره همبافت (شید | لات)","value"=>$request->lot_number??""])


                            @include(
                            "component.input._aotocomplet2",
                            ["id"=>"degree_id",
                            'label'=>"درجه کالا",
                            "option"=>$degree_option["items"],
                            "val"=>$degree_option["value"],
                            "text"=>$degree_option["text"]
                        ])

                        </div>

                    </div>
                    <div class="col-md-12">
                        @if(isset($request->lot_number))
                            @include("component.input._hidden",["id"=>"new_lot_number","value"=>$request->lot_number??""])

                            <div class="alert alert-warning">
                                شماره همبافت (شید | لات) {{$request->lot_number}} در سیستم یافت نشد، آیا تمایل دارید شماره جدید تعریف کنید.
                            </div>
                            <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                            <button type="submit" class="btn btn-success">تعریف شماره همبافت جدید   و ادامه</button>

                        @else
                            <a href="{{route("warps.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                                                 <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>
                            @endif
                    </div>
                </div>

            </div>
        </div>

    </form>


    </div>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection


@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",
            }
        });
    </script>
@endsection


