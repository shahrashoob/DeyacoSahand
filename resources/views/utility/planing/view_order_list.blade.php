@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","کارتابل برنامه ریزی ")
@section("content")

    <div class="row">




        @include("utility.planing._order_list")

        @if(isset($production))
            @include("utility.planing._production_card")
        @endif
    </div>
@endsection
