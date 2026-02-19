@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <form id="form1" action="{{route("hr.post.submit_manage_machine_type_status",[$post,$machine_type])}}" method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h5>
                            مدیریت دسترسی به وضعیت گروه ماشین های <b>{{$machine_type->caption}}</b>
                        </h5>
                    </div>
                    <div class="card-block">


                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-styling">
                                    <thead>
                                    <th></th>
                                    <th>
                                        <input type="checkbox" id="select_all_module">
                                        انتخاب همه
                                    </th>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($status_list as $item)
                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>

                                                <input class="myCheckBox_module" type="checkbox"
                                                       id="switch-data[{{$item->id}}]"
                                                       name="data[status][{{$item->id}}]" {{$post->has_order_status_permission($item->id,3, $machine_type->id)?"checked='checked'":""}}
                                                >
                                                <b> {{$item->id." - ".$item->caption}}</b>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>

                            </div>

                            <a href="{{route("hr.post.manage_access",[$post,0,$machine_type->station_id,0,0])}}" class="btn btn-outline-defualt">بازگشت</a>
                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('آیا از ثبت دسترسی ها اطمینان دارید')"> ثبت دسترسی
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h5>
                            مدیریت دسترسی به عملیات  گروه ماشین های <b>{{$machine_type->caption}}</b>

                        </h5>

                    </div>
                    <div class="card-block">

                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-styling">
                                    <thead>
                                    <th></th>
                                    <th>
                                        <input type="checkbox" id="select_all_module_operation">
                                        انتخاب همه
                                    </th>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($button_list as $item)
                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>

                                                <input class="myCheckBox_module_operation" type="checkbox"
                                                       id="switch-data[{{$item->id}}]"
                                                       name="data[button][{{$item->id}}]" {{$post->has_button_permission($item->id,3, $machine_type->id) ?"checked='checked'":""}}
                                                >
                                                <b>{{$item->id}}- {{$item->caption}}</b>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>

                            </div>

                            <a href="{{route("hr.post.manage_access",[$post,0,$machine_type->station_id,0,0])}}" class="btn btn-outline-defualt">بازگشت</a>
                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('آیا از ثبت دسترسی ها اطمینان دارید')"> ثبت دسترسی
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section("scripts")
    <script>

        $("#select_all_module").change(function () {

            $(".myCheckBox_module").prop('checked', $("#select_all_module").is(':checked'));
        })
        $("#select_all_module_operation").change(function () {

            $(".myCheckBox_module_operation").prop('checked', $("#select_all_module_operation").is(':checked'));
        })
    </script>
@endsection
