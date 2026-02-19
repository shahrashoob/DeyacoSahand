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
                            <th>نام مشخصه</th>
                            <th>ورژن {{$product_version->version_code}}</th>
                            <th> ورژن {{$before_product_version->version_code??"---"}}</th>

                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0; @endphp
                        @foreach($all_properties as $key=>$property)
                        <tr class='{{(isset($current_property[$key])?$current_property[$key]:"") != (isset($before_property[$key])?$before_property[$key]:"")?"alert-warning":""}}'>
                            <td>{{++$row}}</td>
                            <td>{{$property->caption}}</td>
                            <td> {{isset($current_property[$key])?$current_property[$key]:""}}</td>
                            <td> {{isset($before_property[$key])?$before_property[$key]:""}}</td>
                        </tr>
                        @endforeach


                        </tbody>
                    </table>
                </div>

                <div>
                    @if(isset($product_creation_process))
                        <a href="{{route($route_path."index",[$product_creation_process??null])}}" class="btn btn-outline-dark">بازگشت</a>

                    @else
                    <a href="{{route($route_path."index",[$product,$product_creation_process??null])}}" class="btn btn-outline-dark">بازگشت</a>
                    @endif

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

