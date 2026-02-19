@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","انتقال اطلاعات به نرم افزار مالی ")
@section("content")



    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> سابقه تراکنش برای فرم انتقال {{$financial_software_transfer_form->code}}
                      </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>تاریخ</th>
                                <th>اقدام کننده</th>
                                <th>کد رهگیری</th>
                                <th>نرم افزار مالی</th>
                                <th>وضعیت</th>


                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>{{$item->create_datetime()}}</td>
                                    <td>{{$item->worker->fullname()}}</td>
                                    <td>{{$item->id}}</td>
                                    <td>
                                        {{$item->financial_software->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->status->caption??""}}

                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="6" style="border: 0;padding: 0">
                                        @if($item->result!="")
                                            <div class="collapse show " id="result{{$item->id}}" style="">
                                                <div class="" style="border: 2px solid">

                                                    {{$item->result}}

                                                </div>
                                            </div>
                                        @endif
                                        @if($item->message)
                                            <div class="alert alert-info">
                                                {{$item->message->text??""}}
                                            </div>
                                        @endif


                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>
                <div class="center">
            @php $status_name=$financial_software_trans_kind_type->caption_en;@endphp

                    @if($financial_software_transfer_form->$status_name== 5103800)
                        <a href="{{route("wh.financial_software.review",[$financial_software_transfer_form->id,$financial_software_trans_kind_type->id])}}?page={{$page}}" onclick="return confirm('آیا از ثبت اطمینان دارید؟')"
                           class="btn btn-primary">بررسی مجدد</a>
                        <a href="{{route("wh.financial_software.no_need_to_register",[$financial_software_transfer_form->id,$financial_software_trans_kind_type->id])}}?page={{$page}}" onclick="return confirm('آیا از عدم نیاز به ثبت تراکنش اطیینان دارید؟')"
                           class="btn btn-danger">عدم نیاز به ثبت</a>
                    @endif
                    <a href="{{route("wh.financial_software.form_list")}}?page={{$page}}" class="btn btn-outline-dark">بازگشت</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("styles")
    @include("component.input.datepicker._script")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection


@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "start_date_value": "required",
                "end_date_value": "required",
                "warehouse_id_auto": "required",
                "input_output_type": "required"
            }
        });
    </script>
@endsection
