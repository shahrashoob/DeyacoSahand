@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  کنترل کیفیت  ")

@section('content')
    <form id="form1" action="{{route("wh.out.exit_form.submit_QR",[$form])}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">
            @include("component.input._hidden",["id"=>"confirm_type","value"=>""])

            @include("warehouse.out.exit_form.qr._index")


            <div class="col-md-12 center">
                <a href="{{route($route_back??"dashboard")}}"
                   class="btn btn-outline-dark">بازگشت</a>
                @php $not_confirm=false; @endphp
                @switch($form->status_id)
                    @case( 500000535)
                        @if (  \Auth::user()->posts->first()->checkButtonPermission( "quality_control.reject_product.cheek_quality.index" ) )

                            {{--                                در انتظار تایید کنترل کیفیت--}}
                            <button type="submit" class="btn btn-primary"
                                    onclick="return confirm_form('')">
                                تایید کنترل کیفیت
                            </button>
                            @php $not_confirm=true; @endphp
                        @endif
                        @break
                @endswitch

                @if(  $not_confirm)
                    {{--                                در انتظار تایید خروج توسط نگهبانی--}}
                    <button type="submit" class="btn btn-danger"
                            onclick="return reject_form()">
                        عدم تایید
                    </button>
                @endif
            </div>
        </div>

    </form>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    <script>

        function reject_form() {
            $("#confirm_type").val("reject");
            return confirm("آیا از عدم تایید فرم خروج اطمینان دارید؟");
        }

        function confirm_form(text) {
            $("#confirm_type").val("confirm");
            if (text == '')
                return confirm("آیا از  تایید فرم خروج اطمینان دارید؟");
            else
                return confirm(text);
        }
        $('#form1').validate({
            rules: {
                x: "required",
            }
        });
    </script>

@endsection





