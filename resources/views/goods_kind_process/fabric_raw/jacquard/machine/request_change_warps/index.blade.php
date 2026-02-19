@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  درخواست تعویض چله {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.machine.request_change_warps.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="w-100"></div>

                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>

                                </th>
                                <th>خط ورودی</th>
                                <th>کد کالا</th>
                                <th>نام کالا</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($warps_list as $warps_item )

                                    <tr>
                                        <td>
                                        <input type="checkbox" checked name="material_selected[{{$warps_item->id}}]" />
                                        </td>
                                        <td> ورودی {{$warps_item->input_line_code}} ({{$warps_item->goods_kind->caption}})</td>

                                        <td>{{$warps_item->material->code}}</td>
                                        <td>{{$warps_item->material->caption}}</td>

                                    </tr>

                            @endforeach
                            </tbody>
                        </table>


                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">ثبت درخواست</button>
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
