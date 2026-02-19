@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت عملیات فرعی
                        <b>
                            {{ $station_sub_operation->caption}}
                        </b>
                        از عملیات
                        <b>
                            {{$station_operation->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">

                    <form id="form" action="{{route("line_product_station.station.sub_operation.update",$station_sub_operation)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان عملیات  ","value"=>$station_sub_operation->caption])



                        </div>



                        <button type="submit" class="btn btn-primary"> ویرایش</button>
                        <a href="{{route("line_product_station.station.sub_operation.index",$station_operation)}}"
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
        $('#form').validate({
            rules: {
                "caption": "required",
                "station_operation_type_id_auto": "required",
            }
        });
    </script>
@endsection
