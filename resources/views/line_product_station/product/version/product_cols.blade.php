@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")

    <div class="row">
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5> {{$product->code." - ".$product->caption}}

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام ستون</th>
                                <th>ورژن {{$product_version->version_code}}</th>
                                <th> ورژن {{$before_product_version->version_code??"---"}}</th>

                            </tr>

                            </thead>
                            <tbody>


                            <tr class='{{$product_version->weight != ($before_product_version->weight??"")?"alert-warning":""}}'>
                                <td>3</td>
                                <td>وزن کالا</td>
                                <td> {{$product_version->weight}}</td>
                                <td>  {{$before_product_version->weight??""}}</td>
                            </tr>

                            <tr class='{{$product_version->frame_ratio_unit2 != ($before_product_version->frame_ratio_unit2??"")?"alert-warning":""}}'>
                                <td>4</td>
                                <td>نسبت واحد فرعی 2 به واحد اصلی</td>
                                <td> {{$product_version->frame_ratio_unit2}}</td>
                                <td>  {{$before_product_version->frame_ratio_unit2??""}}</td>
                            </tr>


                            </tbody>
                        </table>
                    </div>

                    <div>
                        <a href="{{route($route_path."index",[$product])}}" class="btn btn-outline-dark">بازگشت</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "code": "required",
            }
        });
    </script>
@endsection

