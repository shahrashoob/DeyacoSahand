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
                                <th> SP</th>
                                <th>ورژن {{$product_version->version_code}}</th>
                                <th> ورژن {{$before_product_version->version_code??"---"}}</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0; @endphp
                            @foreach($all_bom_permutation as $key=>$item)
                                <tr class='{{in_array($key,$current_bom_permutation) && in_array($key,$before_bom_permutation)?"":"alert-warning"}}'>
                                    <td>{{++$row}}</td>
                                    <td>SP {{$item->id}}</td>
                                    <td> {!! in_array($key,$current_bom_permutation)?"<span class='fa fa-check'></span>":"" !!}</td>
                                    <td> {!! in_array($key,$before_bom_permutation)?"<span class='fa fa-check'></span>":"" !!}</td>
                                </tr>
                            @endforeach


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

