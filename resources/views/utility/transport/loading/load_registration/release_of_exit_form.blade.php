@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])
@section("page_header_title","داشبورد ارسال بار ")

@section('content')



    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> بار شماره: {{$transport->getCode()}} </h5>
                </div>

                <div class="card-block">

                    @include("utility.transport.public._transport_info")

                </div>
            </div>
        </div>


        <div class="col-md-12 ">

            <form id="form1" autocomplete="off"
                  action="{{route("utility.transport.loading.load_registration.submit_release_of_exit_form",[$transport])}}"
                  method="post"
                  novalidate="novalidate">
                @csrf

                <div class="card">
                    <div class="card-header">
                        <h5> لیست برگ های خروج موجود در بار </h5>
                    </div>

                    <div class="card-block">

                        <div class="alert alert-warning">
                            با توجه به اینکه تمامی بسته بندی های داخل برگ های خروج خوانده نشده است، لطفا نسبت به آزاد
                            سازی بسته بندی ها یا ثبت برگ خروج جدید تصمیم گیری کنید.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>

                                    <th>شماره برگ خروج</th>
                                    <th> تعداد بسته بندی</th>
                                    <th> تعداد بارگیری شده</th>
                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($transport->transport_forms as $t_form)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            {{$t_form->form->getCode()}}
                                        </td>
                                        <td>
                                            {{count($form_packing_form_list[$t_form->form_id])}}
                                        </td>
                                        <td>
                                            {{array_sum($form_packing_form_list[$t_form->form_id])}}
                                        </td>
                                        <td>
                                            @if(array_sum($form_packing_form_list[$t_form->form_id])>0)
                                                <input type="radio" name="form[{{$t_form->form_id}}]" value="release" required>
                                                آزاد سازی
                                                <input type="radio" name="form[{{$t_form->form_id}}]" required
                                                       value="new_exit_form"> برگ خروج جدید
                                            @else
                                                <input type="radio" name="form[{{$t_form->form_id}}]" value="delete" required>
                                                حذف برگ خروج از بار

                                            @endif
                                        </td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
                <div class="center">
                    {{--                در حال بارگیری--}}
                    <a href="{{route("utility.transport.loading.load_registration.show_transport",$transport)}}"
                       class="btn btn-outline-dark">بازگشت</a>

                    <button type="submit" class="btn btn-primary" id="btn_submit">
                        تایید بارگیری
                    </button>
                </div>


            </form>
        </div>

    </div>

@endsection
@section("styles")

    @include("component.input.datepicker._script")
    <style>
        .form-group {
            margin: 0px !important;
        }

        .form-control {
            width: 150px !important;
            margin: auto;
        }
    </style>
@endsection
@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                "caption": "required",
            }
        });

    </script>
@endsection
