@extends('layouts.admin._master')
@section("page_header_title"," داشبورد بسته بندی ")
@section("content")
    <form id="form1"
          action="{{route("fabric_raw.packing_form.page_printing.confirm_submit")}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <input type="hidden" value="{{$packing_form_ids}}" name="packing_form_ids">
        <div class="row">

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> تایید نهایی پرینت لیست بسته بندی ها
                        </h5>
                    </div>
                    <div class="card-block">

                        @include("goods_kind_process.fabric_raw.packing_form.page_printing._table")
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
            <div class="col-md-12 center">
                <a class="btn btn-outline-dark" href="{{route("fabric_raw.packing_form.index")}}">بازگشت</a>
                <button type="submit" class="btn btn-primary" >تایید و دانلود فرم</button>
            </div>

        </div>
    </form>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
