@extends('layouts.admin._master')

@section('page_header_title',"داشبورد مدیریت پیمانکاران ")

@section('content')
    <div class="row">

        <div class="col-sm-12">
            <form id="form1"
                  action="{{route("contractor.admin.contractor_allocation.submit_show_product_inventory",[$production,$production_channel_type])}}"
                  method="post" novalidate="novalidate">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5> لیست بسته بندی های مواد اولیه برای پیمان
                            {{$other_production->serial}}
                            <br/>
                            {{$product->fullCaption()}}
                        </h5>

                    </div>
                    <div class="card-block">
                        @if(!$contractor->it_is_coordination_for_sending)
                            <div class="col-md-12 alert-info">
                                با توجه به اینکه نیاز به هماهنگی ارسال وجود ندارد، می توانید بسته بندی های مجاز (ردیف
                                های سبز رنگ) را
                                انتخاب نمایید.
                            </div>
                        @endif
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
                                    <th>وضعیت</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=$list->firstItem(); $sum=0;@endphp
                                @foreach($list as $item)
                                    @php $sum+=$item->final_amount;@endphp
                                    <tr @if(isset($packing_for_production[$item->id])) class="alert alert-success" @endif>
                                        <td>
                                            @if(isset($packing_for_production[$item->id]) )
                                                @if($contractor->it_is_coordination_for_sending)
                                                    <span class="fa fa-times text-danger" title="با توجه به اینکه پیمانکار نیاز به هماهنگی دارد، امکان انتخاب بسته بندی وجود ندارد."></span>
                                                @else
                                                    <input type="checkbox" name="packing_form_ids[{{$item->id}}]"
                                                           @if(in_array($item->id,$selected_packing_form_ids)) checked @endif>
                                                @endif

                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array($item->id,$selected_packing_form_ids))
                                                <a href="{{route("contractor.admin.contractor_allocation.remove_packing_forms",[$production,$item->id,$production_channel_type])}}"
                                                   class="text-danger " type="button">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{$row++}}</td>
                                        <td>
                                            {{$item->get_create_date_and_time()}}
                                        </td>
                                        <td>
                                            <a
                                                    href="{{route("fabric_raw.packing_form.view",$item->id)}}/{{$list->currentPage()}}"
                                                    target="_blank">
                                                {{$item->code}}
                                            </a>


                                        </td>
                                        <td>
                                            {{$item->packing_type->caption??"---"}}
                                        </td>
                                        <td>
                                            {{$item->final_amount}}
                                        </td>


                                        <td>{{$item->status->caption??"---"}}</td>


                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4">جمع کل ({{$product->unit->caption}})</td>
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
                    <a href="{{route("contractor.admin.contractor_allocation.index",[$production,$contractor,$production_channel_type])}}"
                       class="btn btn-outline-dark     " type="button">
                        بازگشت
                    </a>
                    <button type="submit" class="btn btn-warning">انتخاب و ذخیره بسته بندی ها</button>

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

