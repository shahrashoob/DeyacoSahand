@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <form id="form1" style="display: inline" action="{{route("hr.post.machine_permission",[$post,$machine_type])}}" method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> مدیریت دسرسی به ماشین ها در ایستگاه {{$machine_type->station->caption}}</h5>
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-styling">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>دسترسی به ماشین</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($machine_list as $item)
                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>

                                                <input type="checkbox" id="switch-data[{{$item->id}}]"
                                                       name="data[machine][{{$item->id}}]" {{$post->has_machine_permission($item->id)?"checked='checked'":""}}
                                                ">

                                                <b>{{$item->fullCaption()}}</b>


                                            </td>

                                            <td>

                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>

                            </div>

                        </div>

                        <a href="{{route("hr.post.manage_access",[$post,0,$machine_type->station_id,0,0])}}"
                           class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-success"
                                onclick="return confirm('آیا از ثبت دسترسی ها اطمینان دارید')"> ثبت دسترسی
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection
