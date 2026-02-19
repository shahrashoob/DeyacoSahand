@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-6">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>ویرایش گروه های کاری
                    </h5>
                </div>
                <form id="form1"
                      action="{{route("line_product_station.goods_kind.property.update_product_type",[$goods_kind,$goods_kind_property])}}"
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
                                    <th>
                                        <input type="checkbox" id="select_all">
                                        عنوان گروه کالایی
                                    </th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($goods_kind->product_type as $product_type)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            <input class="myCheckBox" type="checkbox"
                                                   name="data[product_type][{{$product_type->id}}]" {{in_array($product_type->id,$product_type_list)?"checked":""}}/>
                                            {{$product_type->caption}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>

                        <a class="btn btn-dark"
                           href="{{route("line_product_station.goods_kind.property.index",$goods_kind_property->goods_kind_id)}}">
                            بازگشت </a>
                        <button type="submit" class="btn btn-success">ذخیره</button>

                    </div>
                </form>

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
        $("#select_all").click(function () {

            $(".myCheckBox").prop('checked', $("#select_all").is(':checked'));
        })
    </script>
@endsection
