@extends('layouts.admin._master')

@section('page_header_title',"داشبورد مدیریت پیمانکاران ")

@section('content')
    <div class="row">

        <div class="col-sm-12">
            <form id="form1"
                  action="{{route("contractor.admin.contractor_allocation_quick.go_to_contractor_allocation",[$contractor,$production_channel_type])}}"
                  method="post" novalidate="novalidate">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5> لیست بسته بندی های مواد اولیه
                            انتخاب شده جهت
                            {{$contractor->caption}}

                            با کانال پیمان
                            ({{$production_channel_type->caption??""}})
                        </h5>

                    </div>
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling" style="text-align: center!important;">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ردیف</th>
                                    <th>تاریخ ایجاد بسته</th>
                                    <th> شماره بسته بندی</th>
                                    <th>نوع بسته بندی</th>
                                    <th>مقدار کل</th>
                                    <th>کارت پیمان</th>
                                    <th>وضعیت</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=$list->firstItem(); $sum=0;@endphp
                                @foreach($list as $item)
                                    @php $sum+=$item->final_amount;@endphp
                                    <tr @if(isset($packing_for_production[$item->id])) class="alert alert-success" @endif>
                                        <td>

                                        </td>
                                        <td>

                                                <a href="{{route("contractor.admin.contractor_allocation_quick.remove_packing_forms",[$contractor,$production_channel_type,$item->packing_form_id])}}"
                                                   class="text-danger " type="button">
                                                    <i class="fa fa-trash"></i>
                                                </a>

                                        </td>
                                        <td>{{$row++}}</td>
                                        <td>
                                            {{$item->packing_form->get_create_date_and_time()}}
                                        </td>
                                        <td>
                                            <a
                                                    href="{{route("fabric_raw.packing_form.view",$item->packing_form_id)}}/{{$list->currentPage()}}"
                                                    target="_blank">
                                                {{$item->packing_form->code}}
                                            </a>


                                        </td>
                                        <td>
                                            {{$item->packing_form->packing_type->caption??"---"}}
                                        </td>
                                        <td>
                                            {{$item->final_amount}}
                                        </td>
                                        <td>
                                            {{$item->production_form_item->production->parent_production->serial??"***"}}
                                        </td>


                                        <td>{{$item->packing_form->status->caption??"---"}}</td>

<td></td>
<td></td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6">جمع کل ({{$item->product->unit->caption}})</td>
                                    <td>{{$sum}}</td>
                                </tr>
                                </tbody>

                            </table>
                        </div>

                        <div class="float-left">
                            نمايش رکوردهای
                            <b>{{$list->firstItem()}}</b>
                            تا
                            <b>{{$list->lastItem()}}</b>
                            از
                            <b>{{$list->total()}}</b>
                            رکورد موجود
                        </div>
                    </div>
                    <div class="text-center">
                        {{$list->links('pagination::bootstrap-4')}}
                    </div>

                </div>
                <div class="col-md-12 center">
                    <br/>
                    <a href="{{route("contractor.admin.contractor_allocation_quick.select_packing_forms",[$contractor,$production_channel_type])}}"
                       class="btn btn-outline-dark     " type="button">
                        بازگشت
                    </a>
                    <a href="{{route("contractor.admin.contractor_allocation_quick.go_to_contractor_allocation",[$contractor,$production_channel_type])}}"
                       class="btn btn-primary" type="button">
                        تایید و ادامه
                    </a>

                </div>
            </form>
            <div>
                راهنما:
            </div>
            <div class="col-md-4 alert-success">
                کارت سطح بالای بسته بندی با کارت پیمان برابر است
            </div>
            @if($contractor->it_is_coordination_for_sending)
                <div class="col-md-4 alert-danger">
                    <i class="fa fa-times   "></i>
                    با توجه به اینکه پیمانکار نیاز به هماهنگی دارد، امکان انتخاب بسته بندی وجود ندارد.
                </div>
            @endif


        </div>

    </div>
@endsection

@section("styles")

@endsection
@section("scripts")

@endsection

