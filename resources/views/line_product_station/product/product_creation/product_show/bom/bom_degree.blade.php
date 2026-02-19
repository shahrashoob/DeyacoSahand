@extends('layouts.admin._master')
@section('page_header_title',$product_creation_process?"داشبورد طراحی کالا":" کارتابل مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست درجه های مجاز
                        {{$material->fullCaption()}}

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد درجه</th>
                                <th> عنوان درجه</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->degree->code??""}}
                                    </td>
                                    <td>
                                        {{$item->degree->caption??""}}
                                    </td>



                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <a href="{{route("line_product_station.product.product_creation.product_show.bom.index",$product_creation_process)}}"
                       class="btn btn-outline-dark">بازگشت</a>
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
                        "degree_id_auto": "required",
                    }
                });
                $("#btn_back1").click(function () {
                    $("#back_to_bom").val(1);
                })
                $("#btn_back2").click(function () {

                    $("#back_to_bom").val(2);
                })
            </script>
@endsection
