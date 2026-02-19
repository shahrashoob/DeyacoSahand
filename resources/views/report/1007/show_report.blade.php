@extends('layouts.admin._master')
@section("page_header_title","گزارش 1007 - راندمان ماشین آلات بافندگی")

@section("content")

<div class="row">
    <div class="col-xl-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>جدول راندمان ماشین آلات بافندگی</h5>
                <div class="card-header-right">
                    <a href="{{route("report.1007.index")}}" class="btn btn-outline-dark">بازگشت</a>
                </div>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>ماشین</th>
                            <th>کل قطب </th>
                            <th>راندمان</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($machine_efficiency as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    {{$item["machine"]->fullCaption()}}
                                </td>
                                <td>
                                    {{$item["sum_contour"]}}
                                </td>
                                <td>
                                    {{$item["efficiency_1"]}} %
                                </td>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
