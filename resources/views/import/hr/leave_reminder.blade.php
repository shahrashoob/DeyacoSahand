@extends('layouts.admin._master')


@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <h3 class="col-md-6">
                            <span class="badge badge-success">تعداد رکورد: {{count($list)}}</span><br/>

                        </h3>
                        <h3 class="col-md-6">

                            <span class="badge  badge-danger">تعداد خطا: {{$error_count}}</span>
                        </h3>

                        <div style="text-align: center" class="col-md-12">

                            <br/>
                            <br/>
                            @if($error_count>0)
                                <div class="alert alert-danger">لطفا پس از رفع خطا های موجود دوباره فایل اکسل را
                                    بارگذاری کنید.
                                </div>
                                <a href="{{route("import.leave_reminder.index")}}" class="btn btn-primary">بارگذاری
                                    مجدد</a>
                            @else
                                <form action="{{route("import.leave_reminder.update",0)}}" method="post">
                                    @csrf
                                    <button class="btn btn-primary" type="submit">اعمال بروزرسانی</button>
                                </form>

                            @endif


                        </div>
                    </div>
                    <br/>
                    <br/>
                    <h5 style="text-align: center"> محتوای فایل اکسل</h5>
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <tr>
                                <th>#</th>
                                <th>کد ملی  </th>
                                <th>نام و نام خانوادگی</th>
                                <th> سال مالی</th>
                                <th> تاریخ شروع  </th>
                                <th> تاریخ پایان </th>
                                <th>مرخصی ابتدای دوره (دقیقه)</th>
                                <th>مرخصی پایان دوره (دقیقه)</th>
                                <th>مقدار باقی مانده مرخصی (دقیقه)</th>
                            </tr>
                            @php $row=1;@endphp
                            @foreach($list as $item)
                                <tr class="{{$item->error==""?"":'alert-danger'}}">
                                    <td>{{$row++}}</td>
                                    <td>{{$item->national_code}}</td>
                                    <td>{{$item->user_id?$item->worker->fullname():"---"}}</td>
                                    <td>{{$item->year}}</td>
                                    <td>{{$item->start_date_jalali}}</td>
                                    <td>{{$item->end_date_jalali}}</td>
                                    <td>{{$item->leave_in_start}}</td>
                                    <td>{{$item->leave_in_end}}</td>
                                    <td>{{$item->leave_remainder}}</td>
                                </tr>
                                @if($item->error!="")
                                    <tr>
                                        <td colspan=4>
                                            {!!$item->error!!}
                                        </td>
                                    </tr>
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
