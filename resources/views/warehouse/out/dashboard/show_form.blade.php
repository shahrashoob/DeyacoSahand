@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  خروج از انبار  ")

@section('content')
    <div class="row">

        @include("warehouse.out.exit_form.qr._index")
        <div class="col-md-12" style="text-align: center">
            <a href="{{route("wh.out.dashboard.view",[$product_request_form,$page])}}"
               class="btn btn-outline-dark">بازگشت</a>

            @php $exit_form_label_printing_type_list=$product_request_form->warehouse->get_exit_form_label_printing_type(); @endphp
            <div class="btn-group mb-2 mr-2">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">دانلود برگ خروج به تفکیک کالا
                </button>
                <div class="dropdown-menu" x-placement="bottom-start"
                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                    @foreach($exit_form_label_printing_type_list as $exit_form_label_printing_type)
                    <a class="dropdown-item"
                       href="{{route("wh.out.exit_form.download",[$product_request_form,$form,$exit_form_label_printing_type->id,"product"])}}">{{$exit_form_label_printing_type->caption2}}</a>
                        @endforeach
                </div>
            </div>

            <div class="btn-group mb-2 mr-2">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">دانلود برگ خروج به تفکیک  بسته بندی
                </button>
                <div class="dropdown-menu" x-placement="bottom-start"
                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                    @foreach($exit_form_label_printing_type_list as $exit_form_label_printing_type)
                    <a class="dropdown-item"
                       href="{{route("wh.out.exit_form.download",[$product_request_form,$form,$exit_form_label_printing_type->id,"packing_form"])}}">{{$exit_form_label_printing_type->caption2}}</a>
                    @endforeach
                </div>
            </div>

        </div>

    </div>




@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection




