@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <form id="form1"
                  action="{{route("line_product_station.production_channel_type.definition.update_machine_types",$productionChannelType)}}"
                  method="post"
                  autocomplete="off"
                  novalidate="novalidate">
                @csrf


                <div class="card">
                    <div class="card-header">
                        <h5> گروه های ماشین مجاز {{$productionChannelType->caption}} </h5>
                    </div>
                    <div class="card-block">
                        <div class="card-block">

                            <div class="table-responsive">
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th></th>
                                        <th>خط تولید</th>
                                        <th> ایستگاه کاری</th>
                                        <th>گروه ماشین</th>
                                        <th></th>
                                        <th>تعداد کانال تولید مجاز (بعدی)</th>
                                        <th>تعداد کانال تولید مجاز (قبلی)</th>
                                    </tr>


                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($machine_type_list as $item)
                                        <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                            <td>{{++$row}}</td>
                                            <td>
                                                <input name="machine_type[{{$item->id}}]" type="checkbox"
                                                       @if(isset($machine_type_production_channel_type[$item->id])) checked @endif>
                                            </td>
                                            <td>
                                                {{$item->Station->line->caption}}
                                            </td>
                                            <td>
                                                {{$item->Station->caption}}
                                            </td>
                                            <td>
                                                @if(isset($machine_type_production_channel_type[$item->id]))
                                                <a
                                                        href="{{ route("line_product_station.production_channel_type.definition.edit_machine_production_channel_type",[$item,$productionChannelType])}}"
                                                >
                                                   {{$item->caption}}
                                               </a>
                                                @else
                                                    {{$item->caption}}
                                                @endif
                                            </td>
                                            <th>
                                                @if(isset($machine_type_production_channel_type[$item->id]))
                                                {{$item->machine_production_channel_type()->where("production_channel_type_id",$productionChannelType->id)->count()}} ماشین مجاز
                                                @endif
                                            </th>
                                            <td>
                                                @if(isset($machine_type_production_channel_type[$item->id]))
                                                    <a
                                                            href="{{ route("line_product_station.production_channel_type.definition.edit_next_ones",[$item,$productionChannelType])}}"
                                                    >
                                                        {{$productionChannelType->get_production_channel_next_ones($item)->count()}}
                                                        کانال
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($machine_type_production_channel_type[$item->id]))
                                                    <a
                                                            href="{{ route("line_product_station.production_channel_type.definition.edit_before_ones",[$item,$productionChannelType])}}"
                                                    >
                                                        {{$productionChannelType->get_production_channel_before_ones($item)->count()}}
                                                        کانال
                                                    </a>
                                                @endif
                                            </td>


                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="7" style="text-align:center ">
                                            <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>
                                        </td>
                                    </tr>
                                    </tbody>

                                </table>


                            </div>

                        </div>
                    </div>
                </div>




                <div class="card">
                    <div class="card-header">
                        <h5> پیمانکاران مجاز  {{$productionChannelType->caption}} </h5>
                    </div>
                    <div class="card-block">
                        <div class="card-block">

                            <div class="table-responsive">
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th></th>
                                        <th>نام پیمانکار</th>
                                    </tr>


                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($contractor_list as $item)
                                        <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                            <td>{{++$row}}</td>
                                            <td>
                                                <input name="contractor[{{$item->id}}]" type="checkbox"
                                                       @if(isset($contractor_production_channel_type[$item->id])) checked @endif>
                                            </td>
                                            <td>
                                                {{$item->caption}}
                                            </td>

                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="7" style="text-align:center ">
                                            <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>
                                        </td>
                                    </tr>
                                    </tbody>

                                </table>


                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش کانال تولید {{$productionChannelType->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("line_product_station.production_channel_type.definition.update",$productionChannelType)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"نام کانال تولید ","value"=>$productionChannelType->caption,"autofocus"=>1])
                            @include("component.input._text",["id"=>"min_capacity",'label'=>"حداقل ظرفیت ","value"=>$productionChannelType->min_capacity])
                            @include("component.input._text",["id"=>"max_capacity",'label'=>"حداکثر ظرفیت ","value"=>$productionChannelType->max_capacity])
                            @include("component.input._text",["id"=>"max_number_of_sequences",'label'=>"تعداد کانال مشابه ","value"=>$productionChannelType->max_number_of_sequences])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"production_channel_category_id",
                                    "label"=>"گروه کانال تولید ",
                                    "option"=>$machine_type_production_channel_type_option["items"],
                                    "val"=>$machine_type_production_channel_type_option["value"],
                                    "text"=>$machine_type_production_channel_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            @include("component.input._color",["id"=>"color",'label'=>"رنگ کانال ","value"=>$productionChannelType->color])

                        </div>

                        <a href="{{route("line_product_station.production_channel_type.definition.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                    </form>

                </div>
            </div>
        </div>


    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "production_channel_category_id_auto": "required",
                "max_number_of_sequences": {required: true, min: 1},
                "min_capacity": {required: true, min: 1},
                "max_capacity": {required: true, min: 1}
            }
        });
    </script>
@endsection
