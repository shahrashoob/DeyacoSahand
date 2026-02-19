@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست انواع نقص های مجاز برای
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
                                <th>عنوان نقص</th>
                                <th>نمودهای بیرونی نقص کالای تولید شده</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($machine_type->machine_fault as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->machine_fault->fullCaption()}}

                                    </td>
                                    <td>
                                        <form id="form1"
                                              action="{{route("line_product_station.machine_type.machine_fault.store_product_fault",[$machine_type,$item])}}"
                                              method="post"
                                              autocomplete="off"
                                              novalidate="novalidate">
                                            @csrf

                                        @foreach($product_fault_list as $product_fault)

                                            <input name="product_fault[{{$product_fault->id}}]" type="checkbox" {{isset($list_value[$item->id."_".$product_fault->id])?"checked=checked":""}}>
                                            {{$product_fault->caption}}

                                        @endforeach

                                            <button class="btn text-primary" type="submit">
                                                <i class="fa fa-save"></i> ذخیره
                                            </button>
                                        </form>

                                    </td>
                                    <td>
                                        <a class="text-danger"
                                           href="{{ route("line_product_station.machine_type.machine_fault.delete",[$machine_type,$item])}}"
                                           onclick="return confirm('آیا از حذف اطمینان دارید؟')"><i
                                                class="fa fa-trash"></i> </a>

                                    </td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>


                    <form id="form1"
                          action="{{route("line_product_station.machine_type.machine_fault.store",[$machine_type])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"machine_fault_id",
                                    "label"=>"نوع نقص ",
                                    "option"=>$machine_fault_option["items"],
                                    "val"=>$machine_fault_option["value"],
                                    "text"=>$machine_fault_option["text"],
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
                "machine_fault_id_auto": "required",
            }
        });
    </script>
@endsection
