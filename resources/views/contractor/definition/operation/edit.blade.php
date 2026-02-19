@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت عملیات
                        <b>
                            {{ $contractor_operation->caption}}
                        </b>
                        از
                        <b>
                            {{$contractor->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">

                    <form id="form" action="{{route("contractor.definition.operation.update",$contractor_operation)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان عملیات  ","value"=>$contractor_operation->caption])



                        </div>



                        <button type="submit" class="btn btn-primary"> ویرایش</button>
                        <a href="{{route("contractor.definition.operation.index",$contractor)}}"
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
            }
        });
    </script>
@endsection
