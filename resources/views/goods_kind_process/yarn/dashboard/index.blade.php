@extends('layouts.admin._master')
@section("page_header_title","داشبورد ریسندگی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
{{--            @include("orders._search_view",["route"=>"wh.product.list"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست فرم های ریسندگی</h5>
                    <a href="{{route("yarn.production_form.implementation_period_form.index")}}" class="btn btn-success">
                        فرم تولید نخ (ویژه دوره پیاده سازی)
                    </a>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد فرم </th>
                                <th>کاربر ایجاد کننده</th>
                                <th>انبار</th>
                                <th>  وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                        <td>
                                            <a href="{{route("yarn.dashboard.show_form",$item)}}" >{{$item->code()}}</a>
                                        </td>
                                        <td>{{$item->worker->fullname()}}</td>
                                        <td>{{$item->warehouse->caption??""}}</td>
                                        <td>{{$item->status->caption??""}}</td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$list->firstItem()}}</b>
                        تا
                        <b>{{$list->lastItem()}}</b>
                        از
                        <b>{{$list->total()}}</b>
                        رکورد موجود


                    </div>
                </div>
                <div class="text-center" >
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    @endsection
