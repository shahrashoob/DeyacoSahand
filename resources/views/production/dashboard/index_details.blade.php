@extends('layouts.admin._master')
@section("page_header_title","داشبورد جاری تولید")

@section("content")
    <div class="row">

        <div class="col-sm-12">
{{--            @include("production.dashboard._search_view",["route"=>"production.dashboard.index_details"])--}}
            @include("production.dashboard._list_details",["route"=>"production.dashboard.index_details"])

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>

        .table-responsive {
            height: 100vh;    /* ارتفاع قابل اسکرول */
            overflow-y: auto;
            border: 1px solid #ccc;
        }
        thead th {
            position: sticky;
            top: -1px;
            background: #f7f7f7;   /* رنگ پس‌زمینه برای خوانایی */
            z-index: 10;
        }


        .btn_search {
            padding: 3px;
            margin-top: 10px;
            float: left;
            margin-left: 17px;
            font-size: 10px;
        }
        select {

              margin-top: 10px;
              margin-left: 17px;
              font-size: 10px;
          }
        .dropdown-toggle::after{
            border: none;
        }
        .progress{
            height: 6px;
            background: #c5bcbc;
        }
        .table td, .table th
        {
           padding:  .20rem .20rem !important;
            vertical-align: middle !important;
        }
        .modal-dialog{
            max-width: 1500px;
            margin: auto;
        }
    </style>
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

            get_new_option(
                $("#goods_kind_property_id2").val(),
                $("#goods_kind_id").val(),
                "مشخصه کالایی",
                "goods_kind_property_id2",
                "get_property_by_goods_kind",
            )
        })
        $("#goods_kind_property_id").change(function () {
            update_input_search();
        })
        $("#goods_kind_property_id2").change(function () {
            update_input_search2();
        })

        function update_input_search() {

            // مشخصه اول
            request = $.ajax({
                url: "{{url("api/other/get_property_input_by_property_id")}}",
                type: "post",
                data: {
                    "id": $("#goods_kind_property_id").val(),
                    "value": $("#search_goods_kind_property").val()
                }
            });
            request.done(function (response, textStatus, jqXHR) {

                $("#search_input").html(response);
                // حذف label داخل div

                $('#search_input label').remove();
                // حذف کلاس form-control از select
                $('#search').removeClass('form-control');

                // تغییر name از search به search_o
                $('#search')
                    .attr('id', 'search_goods_kind_property')
                    .attr('name', 'search_goods_kind_property');
                // افزودن استایل width:100%
                $('#search').css('width', '100%');
                $('#search').css('margin-top', '10px');
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });





        }

        function update_input_search2(){
            // مشخصه دوم

            request = $.ajax({
                url: "{{url("api/other/get_property_input_by_property_id")}}",
                type: "post",
                data: {
                    "id": $("#goods_kind_property_id2").val(),
                    "value": $("#search_goods_kind_property2").val()
                }
            });
            request.done(function (response, textStatus, jqXHR) {

                $("#search_input2").html(response);
                // حذف label داخل div

                $('#search_input2 label').remove();
                // حذف کلاس form-control از select
                $('#search').removeClass('form-control');

                // تغییر name از search به search_o
                $('#search')
                    .attr('id', 'search_goods_kind_property2')
                    .attr('name', 'search_goods_kind_property2');
                // افزودن استایل width:100%
                $('#search').css('width', '100%');
                $('#search').css('margin-top', '10px');
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

    @include("component.modal.md-modal._script")
@endsection
@section("modals")


    @include("component.modal._dynamic",[
        "id"=>"16",
        "theme"=>"",
        "title"=>" جزئیات سفارش",
        "content"=>"OK",
        "btn_class"=>"btn-danger",
        "btn_title"=>"",
        "token_api"=>$token_api,
        "url"=>route("api.order_api.get_desktop_info_api",[0, 1])
    ])

@endsection
