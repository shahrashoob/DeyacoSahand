@extends('layouts.admin._master')

@section('page_header_title'," داشبورد مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تایید ارسال پیامک گروهی </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.notification.dashboard.confirm")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            @include("component.input._lable",["id"=>"","label"=>"متن پیامک","value"=>$message])
                            @include("component.input._hidden",["id"=>"message","value"=>$message])

                            <div class="col-md-12">

                                <a href="{{route("utility.notification.dashboard.index")}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-success">تایید و ارسال پیامک</button>
                                <br/>
                                <br/>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <div class="alert alert-info">
                                        لطف پست های انتخاب شده جهت ارسال پیامک
                                    </div>
                                    <table class="table table-styling">
                                        <thead>
                                        <tr>
                                            <th>#</th>

                                            <th>پست</th>
                                            <th>کاربران</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @foreach($post_list as $item)
                                            <tr>
                                                <td>
                                                    {{++$row}}
                                                    <input type="hidden" id="switch-data[{{$item->id}}]"
                                                           name="data[post][{{$item->id}}]">

                                                </td>
                                                <td>
                                                    <b @if($item->worker()->count()==0) class="text-danger" @endif>{{$item->id}}-{{$item->caption}}</b>
                                                </td>
                                                <td>
                                                    @php $count=0;@endphp
                                                    @foreach($item->worker as $worker)
                                                        @php $count++;@endphp
                                                        <span @if($worker->mobile=="") class="text-danger" @endif>
                                                        {{$worker->fullname()}} ({{$worker->mobile}}),
                                                        </span>
                                                        @if($count % 3==0)
                                                            <br/>
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>

                                    </table>

                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "message": "required"
            }
        });
        $("#select_all").change(function () {
            $(".myCheckBox").prop('checked', $("#select_all").is(':checked'));
        })
    </script>
@endsection
