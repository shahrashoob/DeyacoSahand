@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش  {{$script->code." - ".$script->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.script.".$script->code.".update_machine",$script)}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">


                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5> لیست ماشین ها </h5>
                                    </div>
                                    <div class="card-block">
                                        <table class="table table-bordered center">
                                            @php $row=0;@endphp
                                            <tr>
                                                <th>نام و کد ماشین</th>
                                                <th>نام و کد ماشین</th>
                                                <th>نام و کد ماشین</th>
                                            </tr>
                                            <tr>
                                                @foreach($machine_list as $item)
                                                    @php $key="k".$item->id;@endphp
                                                    <td>
                                                        <input type="checkbox"
                                                               name="data[machines][{{$item->id}}]" value="1"
                                                            {{(isset($data["machines"][$item->id]) && $data["machines"][$item->id] ==1)?"checked":"" }}
                                                        >
                                                        {{$item->caption}}
                                                    </td>
                                                    @if($row % 3==2)
                                            </tr>
                                            <tr>
                                                @endif
                                                @php $row++;@endphp
                                                @endforeach
                                            </tr>
                                        </table>

                                        <div class="float-left">
                                            نمايش رکوردهای
                                            <b>{{$machine_list->firstItem()}}</b>
                                            تا
                                            <b>{{$machine_list->lastItem()}}</b>
                                            از
                                            <b>{{$machine_list->total()}}</b>
                                            رکورد موجود


                                        </div>
                                    </div>
                                    <div class="text-center">
                                        {{$machine_list->links('pagination::bootstrap-4')}}
                                    </div>
                                </div>
                            </div>

                        </div>

                        <br/>
                        <br/>
                        <a href="{{route("utility.script.".$script->code.".edit",$script)}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                    </form>

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
                "cron": "required",
                "active_status_id_auto": "required",
                "time_of_waning_1": "required",
                "time_of_waning_2": "required",
                "time_of_waning_3": "required",
            }
        });
    </script>
@endsection
