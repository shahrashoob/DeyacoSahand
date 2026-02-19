@extends('layouts.admin._master')

@section('page_header_title',"داشبورد انبار / تخلیه بار ")
@section('content')

    {{--        @include("warehouse.input.complated_information_with_value._separation" , $form)--}}
    @include("warehouse.dashboard._general_item_list" , ['hasQuality' => 1])



    <div class="text-center">


        @if( $form->general_items->every(fn($item) => $item->status_id == 5002007))
            <a href="{{route($route_path."confirm_form",$form)}}" class="btn btn-success ">تایید فرم و ثبت تراکنش انبار</a>
        @endif
        <a href="{{route("wh.dashboard.index")}}?page=1"
           class="btn btn-outline-dark"
           style="width: 130px">بازگشت</a>

</div>
@endsection



