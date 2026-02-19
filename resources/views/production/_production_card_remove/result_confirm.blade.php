@extends('layouts.admin._master')

@section('page_header_title'," کارتابل جاری تولید")

@section('content')
    <form id="form1" action="{{route("production.replace_group",$production)}}" method="post" novalidate="novalidate">
        @csrf

        <div class="row">

            <div class="col-sm-12" style="text-align: center">
                <br/><br/>
                <br/>
                <h4>شاخص ارزیابی عملکرد در کارت {{$production->serial()}}</h4><br/> <br/>
                <h4 class="display-4">{{$production->productivity_index}}</h4>
                <br/> <br/>

            </div>
            @if(count($replace_list)>0 && $extraP->amount >0)
            <div class="w-25"></div>
            <div class="col-sm-12 col-md-6 col-md-offset-3">
                <h4>با توجه به اینکه شما
                    <span class="text-c-purple text-bold"
                          style="font-weight:bold;font-size: 28px;">{{$extraP->amount}} {{$production->product->unit->bach_caption??""}}</span>
                    بیشتر از مقدار کارت تولید کرده اید، می توانید کارت زیر را با کارت ثبت شده جایگزین کنید.</h4>
                <div class="card">
                    @php $replace_number=0; @endphp
                    @foreach($replace_list as $item)
                        @php $replace_number+=$item->number; @endphp
                        @if($replace_number <= $extraP->amount)
                            <div class="card-block border-bottom">
                                <div class="row d-flex align-items-center">
                                    <div class="col-auto">
                                        <a href="#ssdf" class="btn-check" data-id="{{$item->id}}">
                                            <i id="i_{{$item->id}}"
                                               class="feather icon-plus-square f-30 text-c-purple  "></i>
                                        </a>
                                        @include("component.input._hidden",["id"=>"pc_".$item->id,"value"=>0])
                                    </div>
                                    <div class="col">
                                        <h3 class="f-w-300">{{$item->serial()}}</h3>
                                        <span class="d-block text-uppercase">{{$item->product->code}} - {{$item->product->caption}}  <b>{{$item->number}} {{$item->product->unit->bach_caption??""}}</b></span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach


                </div>
            </div>
            <div class="col-sm-12" style="text-align: center">

                <button type="submit" class="btn btn-success" id="btn_replace">جایگزین گروهی</button>
                <a href="{{route("production.list")}}" class="btn btn-primary"  onclick="return !confirm('شما می توانید کارت ها را جایگزین کنید،\n آیا تمایل به جایگزین کردن دارید؟')">بازگشت به کارتابل تولید</a>
            </div>
            @else
                <div class="col-sm-12" style="text-align: center">

                    <a href="{{route("production.list")}}" class="btn btn-primary">بازگشت به کارتابل تولید</a>
                </div>

            @endif


        </div>
    </form>
@endsection

@section("scripts")
    <script>
        var count_check = 0;
        $(".btn-check").click(function () {
            var id = "#i_" + $(this).data("id");
            var hidden = "#pc_" + $(this).data("id");
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
                alert("لطفا حداقل یک کارت را برای جایگزین کردن انتخاب کنید.");
                return false;
            }
            return confirm("آیا از جایگزنی کردن " + count_check + " کارت با کارت ثبت شده اطمینان دارید؟")
        })
    </script>
@endsection
