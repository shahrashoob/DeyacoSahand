@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  فروش ")

@section('content')

    <form id="form1" autocomplete="off"
          action="{{route("sales.product_request_permission.submit",[$order])}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>لیست درخواست های در حال تحویل - {{ $product->caption }} </h5>
                    </div>

                    <div class="card-block">

                        <div class="row">


                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-styling center" style="">
                                        <thead>

                                        <tr>
                                            <th>ردیف</th>
                                            <th>برگ خروج</th>
                                            <th>وضعیت برگ خروج</th>
                                            <th> نام مشتری</th>
                                            <th>مقدار کالا</th>


                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @foreach($list as $item)

                                            <tr>
                                                <td>{{++$row}}</td>
                                                <td>

                                                    {{$item->form->code}}
                                                </td>
                                                <td>

                                                    {{$item->form->status->caption}}
                                                </td>
                                                <td>
                                                    {{$item->form->getApplicantCaption()}}
                                                </td>
                                                <td>
                                                   {{$item->amount}}
                                                </td>

                                            </tr>
                                        @endforeach

{{--                                        <tr>--}}
{{--                                            <td colspan="5">جمع کل</td>--}}
{{--                                            <td>{{$sum_amount_request}}</td>--}}
{{--                                            <td>{{$sum_current_delivery}}</td>--}}
{{--                                            <td>{{$sum_amount_sent}}</td>--}}
{{--                                            <td>{{$sum_amount_remaining}}</td>--}}
{{--                                        </tr>--}}
                                        </tbody>
                                    </table>




                                </div>


                            </div>
                            <div class="col-md-12 center">
                                <br/>
                                <a href="{{route("sales.product_request_permission.index",$order)}}"
                                   class="btn btn-outline-dark     " type="button">
                                    بازگشت
                                </a>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection


@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "image_file": "required",
            }
        });

    </script>
@endsection

