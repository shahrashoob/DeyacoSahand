@extends('layouts.admin._master')
@section("page_header_title","داشبورد ارسال بار ")
@section("content")
    <form id="form1" autocomplete="off"
          action="{{route("utility.transport.loading.load_registration.store")}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">

            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <h5>ثبت بار جدید</h5>


                    </div>
                    <div class="card-block">

                        <div class="row">
                            @include("utility.transport.public._create_transport_view")
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-styling">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><input type="checkbox" id="select_all"/></th>
                                            <th>شماره برگ خروج</th>
                                            <th>نام درخواست دهنده</th>
                                            <th>تاریخ ایجاد</th>
                                            <th> وضعیت</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @foreach($list as $item)
                                            <tr>
                                                <td>{{++$row}}</td>
                                                <td>
                                                    <input type="checkbox" class="checkbox_transport"
                                                           name="form[{{$item->id}}]" {{$form->id == $item->id ?"checked":""}}>
                                                </td>
                                                <td>
                                                    <a href="{{route("utility.transport.loading.dashboard.show_form",$item)}}">{{$item->getCode()}}</a>
                                                </td>
                                                <td>
                                                    {{$item->getApplicantCaption()}}
                                                </td>
                                                <td>{{$item->get_create_date()}}</td>
                                                <td>{{$item->status->caption??""}}</td>


                                            </tr>
                                        @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="col-md-12 center">
                <a href="{{route("utility.transport.loading.dashboard.index")}}"
                   class="btn btn-outline-dark">بازگشت</a>

                <button type="submit" class="btn btn-primary"
                        onclick="return confirm('آیا از ثبت بار اطمینان دارید؟')">ثبت بار جدید
                </button>


            </div>


        </div>

    </form>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {

                driver_mobile: {minlength: 11, maxlength: 11},

            }
        });


        $("#select_all").change(function () {
            $(".checkbox_transport").prop('checked', $(this).is(':checked'));

        })
    </script>
@endsection
