@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست کانال های تولید
                        <b>
                            {{ $machine_type->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نوع کانال تولید</th>
                                <th>کانال مجاز بعدی</th>
                                <th>حداقل ظرفیت</th>
                                <th>حداکثر ظرفیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($machine_type->production_channel_types as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->production_channel_type->caption}}

                                    </td>
                                    <td>
                                        <a
                                                href="{{ route("line_product_station.machine_type.production_channel.edit_next_ones",[$machine_type,$item->production_channel_type])}}"
                                        >
                                            {{$item->production_channel_next_ones()->count()}}
                                            عدد کانال
                                        </a>
                                    </td>
                                    <td>
                                        {{$item->production_channel_type->min_capacity}}

                                    </td>
                                    <td>
                                        {{$item->production_channel_type->max_capacity}}

                                    </td>
                                    <td>
                                        <a class="text-danger"
                                           href="{{ route("line_product_station.machine_type.production_channel.delete",[$machine_type,$item->production_channel_type])}}"
                                           onclick="return confirm('آیا از حذف اطمینان دارید؟')"><i
                                                    class="fa fa-trash"></i> </a>

                                    </td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                    <form id="form1"
                          action="{{route("line_product_station.machine_type.production_channel.store",[$machine_type])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"production_channel_type_id",
                                    "label"=>"نوع کانال تولید ",
                                    "option"=>$production_channel_type_option["items"],
                                    "val"=>$production_channel_type_option["value"],
                                    "text"=>$production_channel_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>


                        </div>


                        <button type="submit" class="btn btn-primary"> افزودن</button>
                        <a href="{{route("line_product_station.machine_type.index",$machine_type->station_id)}}"
                           class="btn btn-outline-dark">بازگشت</a>
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
                "production_channel_type_id_auto": "required",
            }
        });
    </script>
@endsection
