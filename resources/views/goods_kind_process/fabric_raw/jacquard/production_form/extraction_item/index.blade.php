@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تولید بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> استخراج فرم {{$production_form->getCode()}} </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.production_form.extraction_item.submit",$production_form)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-styling">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>شماره</th>
                                        <th> لات</th>
                                        <th>کارت تولید</th>
                                        <th>متراژ سیستم</th>

                                    </tr>

                                    </thead>
                                    <tbody>
                                    @foreach($production_form_item_lot_number_can_select_list as $item)
                                        <tr>
                                            <th>
                                                <input type="checkbox"
                                                       name="production_form_item_lot_number[{{$item->id}}]">
                                            </th>
                                            <th>{{$item->production_form_item->getCode()}}</th>
                                            <th>{{$item->lot_number->code}}</th>
                                            <th>{{$item->production_form_item->production->serial()}}</th>
                                            <th>{{round($item->amount,2)}} </th>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if(count($packing_type_option["items"])!=2)
                                <div class="w-100"></div>
                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"packing_type_id",
                                        "label"=>" نوع بسته بندی ",
                                        "option"=>$packing_type_option["items"],
                                        "val"=>$packing_type_option["value"],
                                        "text"=>$packing_type_option["text"],
                                        "class_col"=>""
                                        ])
                                </div>
                            @else
                                @include("component.input._hidden",[
                                        "id"=>"packing_type_id",
                                        "value"=>$packing_type_option["items"][1]["value"],
                                        ])
                            @endif


                            <div class="col-md-12">
                                <a href="{{route("fabric_raw.production_form.view",$production_form)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-success">تایید و ادامه</button>
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
                "packing_type_id_auto": "required",
            }
        });
    </script>
@endsection
