@extends('layouts.admin._master')
@section("page_header_title","داشبورد جاری تولید")

@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("production.dashboard._search_view",["route"=>"production.dashboard.list"])
            @include("production.dashboard._list")

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection
@section("scripts")
    @include("component.script_function.get_new_option")
    <script>

        $("#goods_kind_id").change(function () {
            get_new_option(
                $("#goods_kind_property_id").val(),
                $("#goods_kind_id").val(),
                "مشخصه کالایی",
                "goods_kind_property_id",
                "get_property_by_goods_kind",
            )
        })
        $("#goods_kind_property_id").change(function () {
            update_input_search();
        })

        function update_input_search() {
            request = $.ajax({
                url: "{{url("api/other/get_property_input_by_property_id")}}",
                type: "post",
                data: {
                    "id": $("#goods_kind_property_id").val(),
                    "value": $("#search").val()
                }
            });
            request.done(function (response, textStatus, jqXHR) {

                $("#search_input").html(response);
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });
        }

        update_input_search();

    </script>
@endsection
