@extends('layouts.admin._master')
@section("page_header_title","داشبورد خروج از انبار ")
@section("content")
    <div class="row">


        <div class="col-sm-12">
            @include("warehouse.out.dashboard._search_view",["route"=>"wh.out.dashboard.index"])
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست درخواست های خروج از انبار به تفکیک درخواست</h5>
                </div>
                <div class="card-block">
                    @if($search_exist_form_model)
{{--                        فرم های خروجی که به صورت دستی یا با دستیار دیجیتال خروجی کشیدن--}}
                       @include("warehouse.out.dashboard._exit_form_no_request")
                    @endif
                    <div id="index_rows">
                        @include("warehouse.out.dashboard._index_rows_small")
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$list->firstItem()}}</b>
                        تا
                        <b>{{$list->lastItem()}}</b>
                        از
                        <b>{{$list->total()}}</b>
                        رکورد موجود

                    </div>
                </div>
                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>


    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

    <script>
        // بارگذاری آیتم های درخواست
        const myTimeout = setTimeout(index_rows_loader, 1500);
        function index_rows_loader()
        {
            request = $.ajax({
                url: "{{route("api.product_request_form.get_index_rows_api")}}",
                type: "post",
                headers: {
                    'Authorization': `Bearer {{$bearer_token}}`,
                },
                data: {
                    "product_request_form_ids":$("#product_request_form_ids").val(),
                    "firstItem":$("#firstItem").val(),
                    "currentPage":$("#currentPage").val(),
                    "order_by":'{{$order_by_Option["value"]}}'
                }
            });
            request.done(function (response, textStatus, jqXHR) {
                lock_submit = false;
                $("#index_rows").html(response)

            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                lock_submit = false;
                // Log the error to the console
                alert("اطلاعات نامعتبر است، لطفا دوباره تلاش کنید.")
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
                result_response = true;
            });
        }
    </script>
@endsection
