@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5> لیست وضعیت های ماشین ها در گروه ماشین های <b> {{$machine_type->caption}}</b>

                        ({{$machine_module_type->caption}})

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.machine_status.update_possibility_allocation",$machine_module_type)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="table-responsive">

                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th> کد وضعیت</th>
                                    <th> عنوان وضعیت</th>
                                    <th>تخصیص کارت تولید</th>
                                    <th>استخراج فرم تولید</th>
                                    <td>حداکثر زمان مجاز توقف (دقیقه)</td>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($machine_type->machine_status as $item)
                                    <tr>
                                        <td>{{++$row}} </td>
                                        <td>
                                            {{isset($item->production_status)?$item->production_status->getCode():"***"}}
                                        </td>
                                        <td>
                                            {{isset($item->production_status)?$item->production_status->getCaption():"***"}}
                                        </td>
                                        <td>
                                            <input name="data[production_status_id][{{$item->production_status_id}}]"
                                                   type="checkbox" {{$item->possibility_of_allocation_machine?"checked":""}}>
                                        </td>
                                        <td>
                                            <input name="data[extraction_status_id][{{$item->production_status_id}}]"
                                                   type="checkbox" {{$item->possibility_of_extraction_production_form?"checked":""}}>
                                        </td>
                                        <td class="center">
                                            <input  type="number" style="width: 65px"
                                                    name="data[max_stop_allowed][{{$item->production_status_id}}]" value="{{$item->max_stop_allowed}}"
                                            >
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>
                        <a class="btn btn-outline-dark"
                           href="{{route("line_product_station.machine_type.index",$machine_type->station_id)}}">بازگشت</a>
                        <button class="btn btn-primary" type="submit">ذخیره تغییرات</button>
                    </form>
                </div>

            </div>
        </div>

    </div>

@endsection
