@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> انتخاب حداقل امتیاز برای تایید کمیته </h5>
                </div>
                <div class="card-block">
                    <div class="col-md-12 alert alert-warning">
                        لطفا برای هر کمیته مشخص نمایید که حداقل چند درصد اعضاء کمیته باید مصاحبه را تایید کنند تا مصاحبه از
                        نظر کمیته تایید شده تلقی گردد.
                    </div>
                    <form id="form1" action="{{route("hr.post.post_selection.submit_percent_of_committee",[$selection,$post])}}" method="post"
                           autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @foreach($selection->selection_selector_committee as $item)

                                @include("component.input._number",["id"=>"minimum_percent_of_committee","value"=>$item->minimum_percent_of_committee, 'label'=>"حداقل امتیاز برای تایید کمیته"])
                            @endforeach
                        </div>

                        <a href="{{route('hr.definition.selection.selection.index')}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">افزودن</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "selection_type_id_auto": "required",
            }
        });
    </script>
@endsection
