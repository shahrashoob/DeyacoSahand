@extends('layouts.admin._master')
@section("page_header_title","داشبورد ".$machine_allocation->getTextOfThing("dashboard_caption")."-  ".
$machine_allocation->getTextOfThing("fullCaption")
)
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  {{$machine_allocation->getTextOfThing("production_caption")}} {{$machine_allocation->production->serial()}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("production.public_module.register_production.submit_add_from_order_packing_form",$machine_allocation)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 alert alert-info">
                                لطفا یک یا چند بسته بندی که قصد ارسال برای پیمانکار را دارید، انتخاب کرده و بر روی دکمه
                                "ثبت بسته بندی ها" کلیک نمایید.
                            </div>
                            <table class="table table-hover center">
                                <thead>
                                <tr>
                                    <th class="center">ردیف</th>
                                <th></th>
                                    <th>نام ماده اولیه</th>
                                    <th>درجه</th>
                                    <th>لات</th>
                                    <th>مقدار</th>
                                    <th>کد بسته بندی مشتری</th>
                                    <th>کد بسته بندی پیمانکار</th>
                                    <th>وضعیت ارسال</th>

                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($order_packing_form_list as $order_packing_form)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <th>
                                            {{--                                            در انتظار ارسال--}}
                                            @if($order_packing_form->status_id ==6070001 && !isset($order_packing_form->packing_form))
                                            <input type="checkbox" name="order_packing_form[{{$order_packing_form->id}}]" checked>
                                            @endif
                                        </th>
                                        <td>{{$order_packing_form->material->caption}}</td>
                                        <td>{{$order_packing_form->degree->caption}}</td>
                                        <td>{{$order_packing_form->lot_number_code}}</td>
                                        <td>{{$order_packing_form->amount}}</td>
                                        <td>{{$order_packing_form->packing_form_code}}</td>
                                        <td>{{$order_packing_form->packing_form->code??""}}</td>
                                        <td>{{$order_packing_form->status->caption}}</td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <div class="col-md-12">


                                    <a href="{{route("production.public_module.register_production.index",$machine_allocation)}}"
                                       class="btn btn-outline-dark"> بازگشت</a>
                                    <button type="submit" href="#" class="btn btn-primary"> ثبت بسته بندی ها</button>
                                </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")

@endsection
@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                "amount": {required: true, "min": 1},
            }
        });
    </script>
@endsection
