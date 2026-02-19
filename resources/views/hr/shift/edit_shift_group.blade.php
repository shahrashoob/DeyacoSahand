@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش دسته بندی های : {{$shift->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.shift.update_shift_group",$shift)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-styling">
                                    <thead>
                                    <tr>
                                        <th>نام دسته بندی</th>

                                        @for($k=1;$k<=$shift->number_of_shift_work;$k++)
                                            <th> </th>
                                        @endfor
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @foreach($shift_work_group_type_list as $shift_work_group_type)
                                        <tr>
                                            <th>{{$shift_work_group_type->caption}}</th>
                                            @for($k=1;$k<=$shift->number_of_shift_work;$k++)
                                                <td>
                                                    <input type="checkbox" name="data[{{$shift_work_group_type->id}}][{{$k}}]" {{isset($shift_work_group_type_checked[$shift_work_group_type->id][$k])?"checked='checked'":""}}>
                                                    گروه {{$k}}
                                                </td>
                                            @endfor
                                        </tr>

                                @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <a href="{{route("hr.shift.index")}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"> ذخیره</button>
                            </div>
                        </div>



                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.select2._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                @for($k = 1; $k <= $shift->number_of_shift_work; $k++)
                "{{"shift_work_id_".$k}}": "required",
                @endfor
            }
        });
    </script>
@endsection
