@extends('layouts.admin._master')


@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <h3 class="col-md-6">
                            <span class="badge badge-success">تعداد رکورد: {{$count}}</span><br/>

                        </h3>
                        <h3 class="col-md-6">

                            <span class="badge  badge-danger">تعداد خطا: {{$error_count}}</span>
                        </h3>

                        <div style="text-align: center" class="col-md-12">

                            <br/>
                            <br/>

                            @if($warning!="")
                                <div class="alert alert-warning">
                                    {!! $warning !!}
                                    <br/>
                                </div>
                            @endif
                            @if($error_count>0)
                                <div class="alert alert-danger">
                                    {!! $error !!}
                                    <br/>
                                    لطفا پس از رفع خطا های موجود دوباره فایل اکسل را
                                    بارگذاری کنید.
                                </div>
                                <a href="{{route("hr.shift.upload_shift_work",$shift)}}" class="btn btn-primary">بارگذاری
                                    مجدد</a>

                            @else
                                <a href="{{route("hr.shift.confirm_shift_work",$shift)}}" class="btn btn-primary">
                                    اعمال تغییرات</a>

                            @endif


                        </div>
                    </div>
                    <br/>
                    <br/>
                    <h5 style="text-align: center"> محتوای فایل اکسل</h5>
                    <div class="table-responsive center">
                        <table class="table table-styling">
                            <tr>
                                <th>ردیف</th>
                                <th>نام شیفت</th>
                                <th>شماره گروه شیفت</th>
                                <th>روز سال</th>
                                <th>نوع روز</th>
                                <th>ساعت کار قانونی (دقیقه)</th>
                                <th>تاریخ</th>

                                <th>تاریخ شروع</th>
                                <th>تاریخ پایان</th>
                                <th>توضیحات</th>


                            </tr>
                            @php $row=1;@endphp
                            @foreach($list as $item)
                                <tr class="{{$item->message==""?"":'alert-danger'}}">
                                    <td>{{$item->row_id}}</td>
                                    <td>{{$item->shift_caption??""}}</td>
                                    <td>{{$item->shift_work_id??""}}</td>
                                    <td>{{$item->day??""}}</td>
                                    <td>{{$item->work_day_type_id??""}}</td>
                                    <td>{{$item->legal_working_hours_in_minute??""}}</td>
                                    @if($item->message!="")
                                        <td></td>
                                        <td>{{$item->start_datetime}}</td>
                                        <td>{{$item->end_datetime}}</td>
                                    @else

                                        <td>{{$item->datetime()}} </td>
                                        <td>{{$item->start_datetime()}}</td>
                                        <td>{{$item->end_datetime()}}</td>
                                    @endif
                                    <td>{{$item->description??""}}</td>
                                </tr>
                                @if($item->message!="")
                                    <tr>
                                        <td colspan=7>
                                            {!!$item->message!!}
                                        </td>
                                    </tr>
                                @else

                                @endif
                            @endforeach

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

                    <div class="text-center">
                        <br/>
                        <br/>
                        {{$list->links('pagination::bootstrap-4')}}
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section("styles")

    @include("component.smartwizard.script")
@endsection
