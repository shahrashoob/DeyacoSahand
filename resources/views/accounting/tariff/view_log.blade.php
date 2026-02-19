@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش تعرفه {{$tariff->id}} - {{$tariff->caption}} </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>اقدام کننده</th>
                                <th>تاریخ ویرایش</th>
                                <th>نوع اقدام</th>
                                <th>واحد پول</th>
                                <th>تاریخ شروع</th>
                                <th>تاریخ پایان</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->user->fullname()}}</td>
                                    <td>{{$item->create_datetime()}}</td>
                                    <td>{{$item->status->caption}}</td>
                                    <td>{{$item->currency->caption}}</td>
                                    <td>
                                        {{$item->start_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->end_datetime()}}
                                    </td>
                                    <td>
                                       @if(in_array($item->status->id ,[ 520100530,520100540]))
                                            <a href="{{route("accounting.tariff.download_log_list",[$tariff->id,$item->id])}}"> <i
                                                    class="fa fa-download"></i> دانلود لیست </a>
                                           @endif
                                    </td>

                                </tr>
                                @if($item->message_id !=0)
                                    <tr class="alert-warning">
                                        <td colspan="8">
                                            {!! $item->message->text??"" !!}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
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
            <a href="{{route("accounting.tariff.index")}}" class="btn btn-outline-dark">بازگشت</a>

        </div>

    </div>

@endsection

