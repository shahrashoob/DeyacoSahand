@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","کارتابل برنامه ریزی ")
@section("content")

    <div class="row">

        @php $order_list=$production->order_list;@endphp
        @if(isset($production->order_list))
            @include("utility.planing._order_list")
        @endif
       @include("utility.planing._production_card")

    </div>
@endsection
