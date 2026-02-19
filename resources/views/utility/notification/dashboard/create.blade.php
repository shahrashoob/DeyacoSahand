@extends('layouts.admin._master')

@section('page_header_title'," داشبورد مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ارسال پیامک گروهی </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.notification.dashboard.store")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            @include("component.input._textarea",["id"=>"message","label"=>"متن پیامک"])

                            <div class="col-md-12">

                                <a href="{{route("utility.notification.dashboard.index")}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>
                                <br/>
                                <br/>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <div class="alert alert-info">
                                        لطف پست های مورد نظر جهت ارسال پیامک را انتخاب کنید.
                                    </div>
                                    <table class="table table-styling">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>
                                                <input type="checkbox" id="select_all">
انتخاب همه
                                            </th>
                                            <th>پست</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @foreach($post_list as $item)
                                            <tr>
                                                <td>{{++$row}}</td>
                                                <td>
                                                    <input class="myCheckBox" type="checkbox" id="switch-data[{{$item->id}}]"
                                                           name="data[post][{{$item->id}}]">

                                                </td>
                                                <td>
                                                    <b>{{$item->id}}-{{$item->caption}}</b>
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
