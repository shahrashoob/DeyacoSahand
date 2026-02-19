@extends('layouts.admin._master')
@section("page_header_title"," داشبورد منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست دستگاه های متصل
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive center">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>عنوان</th>
                                <th>شناسه دستگاه</th>
                                <th>نوع دستگاه</th>
                                <th>پلت فرم دستگاه</th>
                                <th>ورژن پلت فرم دستگاه</th>
                                <th>نوع مرورگر دستگاه</th>
                                <th>ورژن مرور گر دستگاه</th>
                                <th>برند دستگاه</th>
                                <th>اولین ورود</th>
                                <th>آخرین ورود</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->caption??""}}</td>
                                    <td>{{$item->mac_address??""}}</td>
                                    <td>{{$item->user_device_type->caption??""}}</td>
                                    <td>{{$item->platform??""}}</td>
                                    <td>{{$item->platform_version??""}}</td>
                                    <td>{{$item->browser??""}}</td>
                                    <td>{{$item->browser_version??""}}</td>
                                    <td>{{$item->device_brand??""}}</td>
                                    <td>{{$item->get_created_at()}}</td>
                                    <td>{{$item->get_updated_at()}}</td>
                                    <td>
                                        <a href="{{route("hr.personal.user_device.destroy",$item)}}"
                                           onclick="return confirm('آیا از حذف دستگاه اطمینان دارید؟')"><i
                                                    class="fa fa-trash text-danger"></i> </a>
                                    </td>

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

                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>

            </div>
            <a href="{{route("hr.personal.index",[$worker,$worker->random])}}" class="btn btn-outline-dark">بازگشت</a>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
