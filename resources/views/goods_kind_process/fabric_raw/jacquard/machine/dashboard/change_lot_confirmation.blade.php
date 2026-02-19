@extends('layouts.admin._master')

@section('page_header_title',"داشبورد جاری تولید -  ")

@section('content')
    <div class="row">
        <div class="col-sm-12 center">

            <br/>
            <br/>
            <br/>
            <h4> همبافت (لات) ماشین <b>{{ $machine->fullCaption() }} </b>
                تغییر یافت، <br/>


            </h4>
            <div class="alert alert-danger" style="font-size: 18px">
                لطفا همبافت (لات) جدید را بر روی پارچه ثبت نمایید.
            </div>
            <div style="padding-top: 20px">
                @php
                    $band_list=[];
                @endphp
                @foreach($productionFromItemLot as $item)
                    @if(!isset($band_list[$item->production_form_item->band_code]))
                        <a href="#" class="btn btn-info btn-lg"
                           style="font-size: 25px">
                            همبافت (لات) باند  {{$item->production_form_item->band_code}}
                            : {{$item->lot_number->code}}</a>
                        @php $band_list[$item->production_form_item->band_code]=1;@endphp
                    @endif
                @endforeach

            </div>

            <br/>
            <br/>
            <br/>
            <a href="{{route("fabric_raw.machine.dashboard.view",$machine)}}" class="btn btn-primary ">بازگشت</a>


            <br/>
            <br/>

        </div>


    </div>

@endsection


