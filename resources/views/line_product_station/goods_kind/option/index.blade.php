@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-6">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>افزودن مشخصه جدید
                    </h5>
                </div>
                <form id="form1" action="{{route("line_product_station.goods_kind.option.store",$goods_kind_property)}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate">
                    @csrf
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>عنوان مشخصه</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @for($i=1; $i<10;$i++)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            <input type="text" name="data[new][{{$i}}]" value=""/>
                                        </td>
                                    </tr>
                                @endfor
                                </tbody>

                            </table>

                        </div>

                        <a class="btn btn-dark"
                           href="{{route("line_product_station.goods_kind.property.index",$goods_kind_property->goods_kind_id)}}">
                            بازگشت </a>
                        <button type="submit" class="btn btn-success"> افزودن موارد جدید</button>

                    </div>
                </form>

            </div>
        </div>
        <div class="col-sm-6">
            <form id="form1" action="{{route("line_product_station.goods_kind.option.update",$goods_kind_property)}}"
                  method="post"
                  autocomplete="off"
                  novalidate="novalidate">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5>لیست انتخابی مشخصه
                            <b>{{$goods_kind_property->caption}}</b>
                            ({{$goods_kind_property->goods_kind->caption??""}})
                        </h5>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>عنوان ایتم</th>
                                    <th>فعال / غیرفعال</th>
                                    <th>رنگ</th>
                                    <td></td>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($goods_kind_property->option as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            <input type="text" name="data[option][{{$item->id}}]"
                                                   value="{{$item->caption}}"/>
                                        </td>
                                        <td>
                                            <input type="checkbox"
                                                   name="data[enabled][{{$item->id}}]" {{$item->enabled?"checked":""}}/>
                                        </td>
                                        <td>
                                            <div class="form-group">

                                                <input type="color" name="data[color][{{$item->id}}]" class=""
                                                       value="{{$item->color}}">
                                            </div>
                                        </td>
                                        <td>

                                            <a class="text-danger"
                                               href="{{route("line_product_station.goods_kind.option.destroy",[$goods_kind_property,$item])}}"><i
                                                        class="fa fa-trash"></i> </a>
                                        </td>


                                    </tr>
                                    @if($item->field_type_id==3)
                                        <tr>
                                            <td></td>
                                            <td colspan="4">
                                                لیست های انتخاب:
                                                &nbsp;
                                                <a class="text-primary"
                                                   href="{{route("line_product_station.goods_kind.option.index",$item->id)}}"><i
                                                            class="fa fa-edit"></i> ویرایش لیست </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                </div>

            </form>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>



@endsection
@section("scripts")

@endsection
