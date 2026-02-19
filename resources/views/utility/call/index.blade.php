
@extends('layouts.admin._master')


@section("content")
    <div class="row">

        {{--        @include("component.alert._primary",["content"=>__("page.header.event.add_new")])--}}

        <div class="col-sm-12">

            <div class="card">
               @include("component.smartwizard.step",["stepInfo"=>$stepInfo,"active"=>1])

                <div class="card-header">
                    <h5>  شروع فرایند فراخوانی </h5>
                    <div class="card-header-right">

                    </div>
                </div>
                <div class="card-block">

                    <div class="col-sm-12">
                     @if(!$call_error)
                        <form action="{{route("call.create")}}" method="post">
                            @csrf
                            <button class="btn btn-primary" type="submit">شروع فراخوانی </button>
                        </form>
                    @else
                        <div class="alert alert-danger">
                            فراخوانی شماره {{$call_error->id}} در حال انجام است یا با خطا مواجه شده، با پشتیبانی فنی تماس بگیرید.
                        </div>
                    @endif

                    </div>

                </div>


            </div>

        </div>

        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5> چک لیست های پردازش</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>تاریخ و زمان</th>
                                <th>شماره فراخوانی</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($last_check_list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->get_datetime()}}</td>
                                    <td>{{$item->call_id}}</td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
@section("styles")

@include("component.smartwizard.script")
@endsection





