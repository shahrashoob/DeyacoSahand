@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> شروع عملیات {{$machine->fullCaption()}}
                    </h5>

                </div>
                <div class="card-block">
                    @if($machine_allocation->line_product_station->production_method)
                        <div class="text-info">
                            {!! nl2br( $machine_allocation->line_product_station->production_method->description??"") !!}
                        </div>
                    @endif
                    <form id="form1"
                          action="{{route("fabric.finishing_machine.machine.start_operation.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        @if(isset($production_form_item_list) && count($production_form_item_list)>0)
                            <div class="table-responsive">
                                <div class="center"><h4> فرم های تولید جهت ورود به ماشین</h4></div>
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <th>ردیف</th>
                                        <th>شماره ردیف فرم</th>


                                        <th>کد مواد اولیه</th>
                                        <th>نام مواد اولیه</th>
                                        <th>کارت تولید</th>
                                        <th>مقدار نهایی</th>
                                        @php $production_form_item_first=$production_form_item_list[0]; @endphp
                                        @if($production_form_item_first->product->sub_unit)
                                            <th>مقدار فرعی</th>
                                        @endif
                                        <th>کد بسته بندی</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($production_form_item_list as $production_form_item)
                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>{{$production_form_item->getCode()}}</td>
                                            <td>{{$production_form_item->product->code}}</td>
                                            <td>{{$production_form_item->product->caption}}</td>
                                            <td>{{$production_form_item->production->serial}}</td>
                                            <td>{{$production_form_item->final_amount}} {{$production_form_item->product->unit->caption??""}}</td>
                                            @if($production_form_item->product->sub_unit)
                                                <td>
                                                    {{$production_form_item->sub_amount}} {{$production_form_item->product->sub_unit->caption??""}}

                                                </td>
                                            @endif
                                            <td>{{$production_form_item->packing_form_item->code??""}}</td>


                                        </tr>
                                    @endforeach

                                    </tbody>

                                </table>
                            </div>

                        @endif
                        <div class="w-100"></div>

                        @if($machine->check_inventory_for_allocation && count($current_input_list) > 0)
                            <div class="col-md-12 center"><h4>لیست ورودی های ماشین</h4></div>
                            @include("goods_kind_process.general.machine.injection_of_material._injection")
                        @endif


                        <br/>
                        {{--                        فرم تولید نداریم و خروجی ماشین ثبت تولید ندارد.--}}
                        @if(!$current_production_form && $packing_type_option)
                            @if( count( $packing_type_option["items"] ) != 1 )
                                @include(
                                "component.input._aotocomplet2",
                                ["id"=>"packing_type_id",
                                'label'=>"نوع بسته بندی",
                                "option"=>$packing_type_option["items"],
                                "val"=>$packing_type_option["value"],
                                "text"=>$packing_type_option["text"],
                                "class_col"=>"col-md-3"
                            ])
                                @if($carrier_has_number_ability)
                                    @include("component.input._number",["id"=>"carrier_id","lable"=>" شماره حامل جدید ","value"=>$carrier_id,
                                   "class_col"=>"col-md-3"])
                                @endif
                            @else
                                @include("component.input._hidden",["id"=>"packing_type_id","value"=>$packing_type_option["items"][0]["value"]])
                                @include("component.input._lable",["lable"=>"نوع بسته بندی","value"=>$packing_type_option["items"][0]["text"]])
                                @if($carrier_has_number_ability)
                                    @include("component.input._number",["id"=>"carrier_id","lable"=>" شماره حامل جدید ","value"=>$carrier_id,
                                                             "class_col"=>"col-md-3"])
                                @endif
                            @endif

                        @else
                            {{--                            @include("component.input._lable",["id"=>"carrier_id","lable"=>"فرم تولید","value"=>$current_production_form->code,--}}
                            {{--                               "class_col"=>"col-md-3"])--}}
                            {{--                            @include("component.input._lable",["id"=>"carrier_id","lable"=>"حامل","value"=>$current_production_form->carrier->code." (".($current_production_form->carrier->carrier_type->caption??"").")",--}}
                            {{--                               "class_col"=>"col-md-3"])--}}
                        @endif
                        <div class="col-md-12">

                            <a href="{{route("fabric.finishing_machine.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>
                            <button type="submit" class="btn btn-success">تایید</button>
                        </div>


                    </form>
                </div>
            </div>

        </div>
        <div class="col-md-12 center">


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
                "var": "required",
            }
        });
    </script>
@endsection