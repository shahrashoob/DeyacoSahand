@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> اعلام نقص ماشین {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route($route_path."submit_machine_contour",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="alert alert-info">
                            <h5>توجه!</h5>
                            با توجه به نقص اعلام شده، کارت تولید جاری ماشین توسط سامانه کنسل شده و یک کارت رزرو جایگزین می گردد،
                            <br/>
                            لطفا پس از ثبت، با توجه به  وضعیت ماشین نسبت به تولید اولین کارت رزرو اقدام نمایید.
                        </div>

                        @if( $has_requirement_for_doffs)
                            @if($machine->production_status_id != 7003017 )
                                <div class="alert alert-warning">
                                    <h5>توجه!</h5>
                                    با توجه به اینکه شما ملزم به داف کردن پارچه در حال بافت می باشید، غلطک پارچه خام
                                    بعدی را
                                    در کنار دستگاه قرار داده و اطلاعات ذیل را تکمیل نمایید.
                                </div>
                            @endif



                            @if( count( $packing_type_option["items"] ) != 1 )
                                @include(
                                "component.input._aotocomplet2",
                                ["id"=>"packing_type_id",
                                'label'=>"نوع بسته بندی",
                                "option"=>$packing_type_option["items"],
                                "val"=>$packing_type_option["value"],
                                "text"=>$packing_type_option["text"]
                            ])
                            @else
                                @include("component.input._hidden",["id"=>"packing_type_id","value"=>$packing_type_option["items"][0]["value"]])

                            @endif
                            @include("component.input._number",["id"=>"carrier_id","lable"=>" شماره غلطک پارچه خام جدید ","value"=>$carrier_id])



                        @endif

                        @include("goods_kind_process.fabric_raw.public._shift_and_counter")

                        @if(isset($carrier_id))
                            <div class="alert alert-danger">
                                با توجه به اینکه حامل
                                {{$carrier_id}}
                                بر روی همین ماشین
                                در حال تکمیل می باشد، آیا از انتخاب حامل اطمینان دارید؟
                                <br/>
                                (توجه داشته باشید که در صورت تایید، شما ملزم به تغییر بسته بندی به قید فوریت می باشید.)
                            </div>
                        @endif


                        <div class="col-md-12">
                            <a href="{{route($route_path."index",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">ثبت و ادامه</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>


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
                "shift_work_id_auto": "required",
                "packing_type_id_auto": "required",
                "contour_1_value": "required",
                "carrier_id": "required"
            }
        });
    </script>
@endsection
