@extends('layouts.admin._master')

@section('page_header_title'," داشبورد جاری تولید - ".$production->product->goods_kind->caption)

@section('content')
    <form id="form1"
          action="{{route("fabric_raw.jacquard.machine_allocation.select_band_submit",[$machine,$production])}}"
          method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">

            <div class="w-25"></div>
            <div class="col-sm-12 col-md-6 col-md-offset-3">
                <h6>شما می توانید
                    {{$production->product->caption??""}}
                    را بر روی
                    {{$band_count_allocation}} باند آزاد
                    ماشین
                    {{$machine->code." ".$machine->caption}}
                    ببافید، در صورت تایید بر روی دکمه ثبت و ادامه کلیک نمایید.
                </h6>
                <div class="card">
                    @php $replace_number=0; @endphp
                    @foreach( $open_band_list as $i)
                        @php $band_code_warps=$i>$warps_count?$warps_count:$i;@endphp

                        <div class="card-block border-bottom">
                            <div class="row d-flex align-items-center">
                                <div class="col-auto">
                                    <a href="#ssdf" class="btn-check" data-id="{{$i}}">
                                        <i id="i_{{$i}}"
                                           class="feather f-30 text-c-green  icon-check-square"></i>
                                    </a>
                                    @include("component.input._hidden",["id"=>"band_".$i,"value"=>$i])
                                    @include("component.input._hidden",["id"=>"band_name_".$i,"value"=>$i])
{{--                                     @include("component.input._hidden",["id"=>"warps_amount_".$i,"value"=>$warps_amount_list[$i]["amount"]])--}}
                                </div>
                                <div class="col">
                                    <h3 class="f-w-300"> باند {{$i}}</h3>
                                    @include("component.input._number",["id"=>"band_amount_".$i,"label"=>"مقدار تخصیص ".$production->serial()." - ".$production->product->caption,"class_col"=>"col-md-12","value"=>$allocation_amount_list[$i] ])
                                    @if(isset($warps_amount_list[$band_code_warps]))
                                        @include("component.input._lable",[
                                                   "id"=>"warps_amount_".$i,
                                                   "label"=>"مقدار مورد نیاز برای کارت های رزرو ".$i,
                                                   "class_col"=>"col-md-12",
                                                   "value"=>$warps_amount_list[$band_code_warps]["sum_amount_reserve"]." متر",

                                                   ])
                                        @include("component.input._lable",[
                                                   "id"=>"warps_amount_".$i,
                                                   "label"=>"مقدار باقی مانده چله در زمان شروع بافت ".$i,
                                                   "class_col"=>"col-md-12",
                                                   "value"=>$warps_amount_list[$band_code_warps]["amount_begin_of_weaving"]." متر",
                                                   "message"=>($warps_amount_list[$band_code_warps]["result"]?null:"<div class='text-danger' style='font-weight: bold'>".$warps_amount_list[$band_code_warps]["message"]."</div>"),

                                                   ])

                                    @endif

                                </div>
                            </div>
                        </div>
@break
                    @endforeach


                </div>
            </div>
            <div class="w-25"></div>
            <div class="w-25"></div>
            @if(count($reserve_after_allocation_option)>0)
                <div class="col-sm-12 col-md-6 col-md-offset-3">
                    <div class="card">
                        <div class="card-block border-bottom">
                            <div class="row d-flex align-items-center">
                                <div class="col-auto">


                                    <i id="i_{{$i}}"
                                       class="fa fa-vial  fa-2x text-success"></i>

                                </div>
                                <div class="col">

                                    @include("component.input._select",["id"=>"reserve_after_allocation_id","label"=>" کارت تولید نمونه گیری
                                    بعد از کارت زیر  بر روی ماشین رزور شود.","option"=>$reserve_after_allocation_option,"class_col"=>"col-md-12"])

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-sm-12" style="text-align: center">

                <button type="submit" class="btn btn-success" id="btn_replace">ثبت تخصیص و ادامه</button>
                <a href="{{route("fabric_raw.production_card.view_card",$production)}}" class="btn btn-outline-dark">بازگشت
                    به کارتابل تولید</a>
            </div>


        </div>
    </form>
@endsection

@section("style")
    <style>
        .card-block {
            padding: 15px 20px !important;
        }
    </style>
@endsection
@section("scripts")
    <script>
        var count_check = "{{$band_count_allocation}}";
        $(".btn-check").click(function () {
            var id = "#i_" + $(this).data("id");
            var hidden = "#band_" + $(this).data("id");
            if ($(id).hasClass("icon-check-square")) {
                $(id).removeClass("icon-check-square").removeClass("text-c-green").addClass("text-c-purple").addClass("icon-plus-square");
                $(hidden).val(0);
                count_check--;
            } else {
                $(id).removeClass("icon-plus-square").removeClass("text-c-purple").addClass("text-c-green").addClass("icon-check-square");
                $(hidden).val(1);
                count_check++
            }
        });

        $("#btn_replace").click(function () {
            if (count_check <= 0) {
                alert("لطفا حداقل یک باند را  انتخاب کنید.");
                return false;
            }

            @foreach( $open_band_list as $i)

            if ($("#band_amount_{{$i}}").val() > {{$allocation_amount_list[$i]}}) {
                alert("مقدار تخصیص باند " + count_check + " بیش از مقدار مجاز می باشد.")
                return false;
            }
            @if(count($reserve_after_allocation_option)>0)
            if ($("#reserve_after_allocation_id").val() == "") {
                alert("لطفا یک کارت تولید را انتخاب کنید.")
                return false;
            }
            @endif
            {{--if (parseFloat($("#band_amount_{{$i}}").val()) > parseFloat($("#warps_amount_{{$i}}").val())) {--}}
            {{--    $result = confirm("توجه! \n" +--}}
            {{--        " باتوجه به اینکه مقدار باقی مانده چله در باند خروجی" + " {{$i}} " +--}}
            {{--        "از مقدار لازم برای بافت مقدار تخصیص داده شده باند خروجی" + " {{$i}} " +--}}
            {{--        "کمتر است، آیا از ادامه تخصیص اطمینان دارید؟");--}}
            {{--    if (!$result) {--}}
            {{--        return false;--}}
            {{--    }--}}
            {{--}--}}

            @endforeach

        })

        $('#form1').validate({
            rules: {
                "reserve_after_production_id": "required",
            }
        });
    </script>
@endsection


