@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار  ")

@section('content')

    @if($search_exist_form_model)
        {{--                        فرم های خروجی که به صورت دستی یا با دستیار دیجیتال خروجی کشیدن--}}
        @include("warehouse.out.dashboard._exit_form_no_request")
    @endif
    <form id="form1" autocomplete="off" action="{{route("wh.out.exit_form_implementation.submit")}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-md-12" id="card-block">
                @include("warehouse.out.exit_form_implementation._packing_form_list")
            </div>

        </div>
    </form>



@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection

@section("scripts")
    @include("warehouse.out.exit_form_implementation._script")
    <script>
        $('#form1').validate({
            rules: {
                "trans_kind_id": "required",
                "opp_kind_id": "required",
                "cost_center_id": "required",
                "description": "required",
            }
        });
    </script>
@endsection
