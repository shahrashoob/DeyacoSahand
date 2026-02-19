@extends('layouts.admin._master')

@section('page_header_title',"داشبورد مدیریت پیمانکاران ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تخصیص شماره {{$machine_allocation->allocation_id}}</h5>
                </div>
                <div class="card-block">


                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>فرم انبار</th>
                                <th>کد کالا</th>
                                <th>نام کالا</th>
                                <th>درجه</th>
                                <th>لات</th>
                                <th>نوع بسته بندی</th>
                                <th>تعداد بسته بندی</th>
                                <th>مقدار کل</th>
                                <th>{{isset($form_general_items->first()->product->sub_unit)?"مقدار فرعی ":""}}</th>
                                <th>مبلغ (ریال)</th>
                                <th>مبلغ ارزش افزوده (ریال)</th>
                                <th>مبلغ کل (ریال)</th>
                                <th> فرم ورود به انبار</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($form_general_items as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>{{$item->form->code??""}}</td>
                                    <td>{{$item->product->code??""}}</td>
                                    <td>{{$item->product->caption??""}}</td>
                                    <td>{{$item->degree->caption??""}}</td>
                                    <td>{{$item->lot_number->code??""}}</td>
                                    @if($item->warehouse_storage_type_id && $item->warehouse_storage_type_id!=2)
                                        <td>{{$item->warehouse_storage_type->caption}}</td>
                                    @else
                                        <td title="{{$item->packing_type->fullCaption()}}"><a
                                                    href="#">{{$item->packing_type->code}}</a></td>
                                    @endif
                                    <td>{{$item->packing_form_number??""}}</td>
                                    <td>{{$item->amount??""}}</td>
                                    <td>{{isset($form_general_items->first()->product->sub_unit)?($item->sub_amount??""):""}}</td>
                                    @if(isset($item->price))
                                        <td>{{number_format($item->price)}}</td>
                                        <td>{{number_format($item->tax_price)}}</td>
                                        <td>{{number_format($item->total_price_with_tax)}}</td>
                                    @else
                                        <td colspan="3" style="text-align: center">
                                           <a href="{{route("supplier.admin.supplier_register.complete_financial_info",$item)}}">
                                               تکمیل اطلاعات مالی
                                           </a>
                                        </td>
                                    @endif
                                    <td>    {{$item->form->code}} ({{$item->form->status->caption}})</td>
                            @endforeach
                            </tbody>

                        </table>
                    </div>


                </div>
            </div>
        </div>

        @include("supplier.admin.dashboard._transport")
        <div class="col-md-12">
            <a href="{{route("supplier.admin.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>
        </div>


    </div>

@endsection

@section("scripts")
    <script>

    </script>
@endsection
