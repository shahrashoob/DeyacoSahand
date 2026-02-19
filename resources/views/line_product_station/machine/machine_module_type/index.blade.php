@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5> لیست تنظیمات  <b> {{$machine_type->caption}}</b>

                        ({{$machine_module_type->caption}})

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.machine.machine_module_type.update",[$machine_type,$machine_module_type])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="table-responsive">

                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>کد ویژگی</th>
                                    <th>نام ویژگی ماشین - ماژول</th>
                                    <td></td>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($property_list as $item)
                                    <tr>
                                        <td>{{++$row}} </td>
                                        <td>
                                            {{ $item->id}}
                                        </td>
                                        <td>
                                            {!! $item->caption !!} ({{$item->special_unit->caption}})
                                        </td>
                                        <td>
                                            @switch($item->field_type_id)
                                                @case(1)
                                                <input required="required" type="number" name="mmtpv_{{$item->id}}"
                                                       value="{{isset($property_value[$item->id])?$property_value[$item->id]:"0"}}">
                                                @break
                                                @case(2)
                                                <input required="required" type="text" name="mmtpv_{{$item->id}}"
                                                       value="{{isset($property_text_value[$item->id])?$property_text_value[$item->id]:"0"}}">
                                                @break
                                            @endswitch
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
