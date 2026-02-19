@extends('layouts.admin._master')


@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">
                    <h5> {{$product->fullCaption()." - ".$goods_kind_property->caption}}  </h5>

                </div>
                <div class="card-block" style="text-align: center">


                    <img src="{{url($file->path)}}" style="max-width: 100%"/>
                    <br/>
                    <br/>
                    <a class="btn btn-outline-dark" href="{{ isset($back_to_edit)?route("line_product_station.product.edit_property",$product):URL::previous() }}">بازگشت</a>

                </div>

            </div>


        </div>

    </div>

@endsection
@section("styles")

@endsection





