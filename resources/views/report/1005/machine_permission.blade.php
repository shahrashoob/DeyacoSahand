@extends('layouts.admin._master')
@section("page_header_title","گزارش 1005 - وضعیت ماشین آلات")
@section("content")
    <form id="form1" style="display: inline" action="{{route("report.1005.machine_permission",[$machine_type])}}" method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> انتخاب  ماشین  {{$machine_type->caption}}</h5>                    </div>
                    <div class="card-block">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-styling">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>انتخاب ماشین</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($machine_list as $item)
                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>

                                                <input type="checkbox" id="switch-data[{{$item->id}}]"
                                                       name="data[machine][{{$item->id}}]" {{$report_1005_line->has_machine_permission($item->id)?"checked='checked'":""}}
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


                        <a href="{{route("report.1005.index")}}" class="btn btn-outline-secondary">بازگشت </a>

                        <button type="submit" class="btn btn-success"
                               > انتخاب
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection
