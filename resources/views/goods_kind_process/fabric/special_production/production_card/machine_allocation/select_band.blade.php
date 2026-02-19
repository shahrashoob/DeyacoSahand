@extends('layouts.admin._master')

@section('page_header_title'," داشبورد جاری تولید - ".$production->product->goods_kind->caption)

@section('content')
    <form id="form1" action="{{route("fabric.special_production.machine_allocation.confirm_submit",[$machine,$production])}}"
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

                    @include("component.input._hidden",["id"=>"production_id","value"=>$production->id])
                    @foreach( $open_band_list as $i)

                        <div class="card-block border-bottom">
                            <div class="row d-flex align-items-center">
                                <div class="col-auto">
                                    <a href="#ssdf" class="btn-check" data-id="{{$i}}">
                                        <i id="i_{{$i}}"
                                           class="feather f-30 text-c-green  icon-check-square"></i>
                                    </a>
                                    @include("component.input._hidden",["id"=>"band_".$i,"value"=>$i])
                                    @include("component.input._hidden",["id"=>"band_name_".$i,"value"=>$i])
                                </div>
                                <div class="col">
                                    <h3 class="f-w-300"> باند {{$i}}</h3>
                                    @include("component.input._number",["id"=>"band_amount_".$i,"label"=>"مقدار تخصیص ".$production->serial()." - ".$production->product->caption,"class_col"=>"col-md-12","value"=>$allocation_amount_list[$i] ])

                                </div>
                            </div>
                        </div>

                    @endforeach


                </div>
            </div>
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
                <a href="{{route("fabric.production_card.view_card",$production)}}" class="btn btn-outline-dark">بازگشت
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
            return confirm("آیا از انتخاب " + count_check + " کارت تولید برای باندهای ماشین اطمینان دارید؟")
        })
    </script>
@endsection


