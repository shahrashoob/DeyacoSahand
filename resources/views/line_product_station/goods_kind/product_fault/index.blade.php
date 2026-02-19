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
                            {{ $goods_kind->caption}}
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
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($goods_kind->product_fault as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->product_fault->fullCaption()}}

                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <a class="text-danger" href="{{ route("line_product_station.goods_kind.product_fault.delete",[$goods_kind,$item])}}" onclick="return confirm('آیا از حذف اطمینان دارید؟')"><i class="fa fa-trash"></i> </a>

                                    </td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>


                    <form id="form1" action="{{route("line_product_station.goods_kind.product_fault.store",[$goods_kind])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"product_fault_id",
                                    "label"=>"نوع نقص ",
                                    "option"=>$product_fault_option["items"],
                                    "val"=>$product_fault_option["value"],
                                    "text"=>$product_fault_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>


                        </div>


                        <button type="submit" class="btn btn-primary"> افزودن </button>
                        <a href="{{route("line_product_station.goods_kind.index")}}"
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
                "product_fault_id_auto": "required",
            }
        });
    </script>
@endsection
