@extends('layouts.admin._master')

@section('page_header_title',"داشبورد بسته بندی  ")

@section('content')
    @php
        if(!function_exists("to_persian")){
            function to_persian($string) {
                $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

                $output= str_replace($english,$persian,  $string);
                return $output;
            }
        }

    @endphp
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  بسته بندی {{$packing_form->code}}</h5>
                </div>
                <div class="card-block ">

                    <form action=" {{route("fabric_raw.packing_form.print_qr.submit",$packing_form)}}" method="post">
                        @csrf

                        <div class="table table-bordered"
                             style="max-width: 700px; overflow: auto; border: 3px solid #0b0b0b; padding: 5px;text-align: center; margin: auto">


                            @include("goods_kind_process.fabric_raw.packing_form.print_qr.template".$packing_form->packing_type->packing_type_label_printing_type_id."._print_info")

                        </div>
                        <div class="table table-bordered"
                             style="max-width: 700px;  padding: 5px;text-align: center; margin: auto">
                            @if($back_url_route!="")

                                @include("component.input._hidden",["id"=>"back_url_route","value"=>$back_url_route])
                                @include("component.input._hidden",["id"=>"id","value"=>$id])

                                <a href="{{route($back_url_route,$id)}}"
                                   class="btn btn-outline-dark" style="width: 100px">بازگشت</a>

                                <button type="submit" class="btn btn-primary" style="width: 100px">تایید</button>
                            @else

                                <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
                                   class="btn btn-outline-dark" style="width: 100px">بازگشت</a>
                                <button type="submit" class="btn btn-primary" style="width: 100px">چاپ</button>
                                <a href="{{route("fabric_raw.packing_form.print_qr.download",$packing_form)}}"  class="btn btn-primary"
                                   class="" style="width: 100px"> دانلود </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>

@endsection

@section("scripts")
    <style>
        .content table {
            max-width: 600px;
            margin: auto;
        }

    </style>
@endsection
