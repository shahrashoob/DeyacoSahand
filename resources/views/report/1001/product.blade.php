@extends('layouts.admin._master')
@section("page_header_title","داشبور جاری تولید")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5> گزارش 1001 - در انتظار تولید ( به تفکیک  محصول) - {{$product->code." - ".$product->caption}}</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>سریال تولید</th>
                                <th>تعداد</th>
                                <th>واحد</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($production as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a target="_blank" href="{{route("production.dashboard.view_card",$item->id)}}">{{$item->serial(1)}}</a>
                                    </td>
                                    <td>{{$item->number}}</td>
                                    <td>{{$item->product->unit->bach_caption}}</td>
                                    <td>{{$item->getStatus()}}</td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>


                </div>

            </div>
        </div>
        <div class="col-md-12" style="text-align: center">
            <a href="{{route("report.1001.line_group",$product->line_group_id)}}" class="btn btn-dark"> بازگشت</a>

        </div>

    </div>

@endsection

