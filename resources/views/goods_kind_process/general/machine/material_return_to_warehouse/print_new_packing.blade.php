@extends('layouts.admin._master',["keypress_enable"=>1])

@section('page_header_title',"داشبورد  تولید  ")

@section('content')
    <div class="row">
        <div class="col-sm-12" id="card">
            <div class="card">
                <div class="card-header">
                    <h5>
                        لیست بسته بندی های جدید جهت چاپ برچسب

                    </h5>
                </div>

                <div class="card-block">
                    <form id="form1"
                          action="{{route($route_path."submit_print_new_packing",[$machine,$machine_allocation_modification])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            <input type="checkbox" id="select_all">
                                        </th>
                                        <th> کد بسته بندی</th>
                                        <th>نوع بسته بندی</th>
                                        <th>مقدار کل</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=1;@endphp
                                    @foreach($packing_form_list as $item)
                                        <tr>
                                            <td>{{$row++}}</td>
                                            <td>

                                                <input class="myCheckBox }"
                                                       name="data[packing_form][{{$item->id}}]"
                                                       id="packing_{{$item->id}}"
                                                       data-id="{{$item->id}}"
                                                       type="checkbox" checked>

                                            </td>
                                            <td>{{$item->getCode()}}</td>
                                            <td>{{$item->packing_type->caption??""}}</td>
                                            <td>
                                                @if(isset($unconfirmed_data_list[$item->id]))
                                                    {{$unconfirmed_data_list[$item->id]["final_amount"]}}
                                                @else
                                                    {{$item->getFinalAmount()}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>

                        </div>


                        <div style="text-align: center">

                            <a href="{{route($dashboard_route."view",[$machine])}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary">انتخاب و چاپ</button>

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
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",
            }
        });

    </script>
@endsection

