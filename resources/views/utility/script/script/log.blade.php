@extends('layouts.admin._master',["no_persian"=>1])
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>سابقه اجرای  {{$script->caption}}</h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره مرجع</th>
                                <th>تاریخ و ساعت</th>
                                <th>ماشین</th>
                                <th> اقدام کنننده</th>
                                <th> رویداد</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->id}}</td>
                                    <td>{{$item->datetime()}} </td>
                                    <td>
                                        {{$item->machine->caption??""}}
                                    </td>
                                    <td>{{$item->worker->fullname()??""}}</td>
                                    <td>
                                        {{$item->event->caption??""}}
                                    </td>


                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <div class="center">
                                            <a href="#!" data-toggle="collapse" data-target="#s{{$item->id}}"
                                               aria-expanded="true" aria-controls="s{{$item->id}}" class="">
                                                مشاهده جزئیات
                                            </a>
                                        </div>

                                        <div class="col-md-12 collapse " id="s{{$item->id}}">
                                            @include("component.json_data.json_data_view",["object"=>$item])
                                        </div>
                                    </td>
                                </tr>
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
        </div>
    </div>
@endsection
@section("scripts")
    <script>


    </script>
@endsection


