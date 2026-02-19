@extends('layouts.admin._master')
@section("page_header_title","کارتابل همکاری با ما")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("hr.employment.admin.dashboard._search_view",["route"=>"hr.employment.admin.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5> لیست درخواست های همکاری با ما
                    </h5>

                    @if($allow_direct_register)
                        <a href="{{route("hr.employment.admin.direct_register.index")}}">
                            ثبت نام مستقیم (مشتری، تامین کننده و پیمانکار)
                        </a>
                    @endif
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>نام  و نام خانوادگی/نام شرکت</th>
                                <th>نوع شخصیت</th>
                                <th>نوع همکاری با سازمان</th>
                                <th>شناسه یکتا</th>
                                <th>شماره همراه</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row = ($list->currentPage() - 1) * $list->perPage(); @endphp
                            @foreach($list as $item)

                                <tr>
                                    <td>{{++$row}}</td>

                                    <td>
                                        @if($item->personal_type_id==1)
                                        @if($item->status_id !=4640301)
                                        <a href="{{route("hr.employment.admin.dashboard.view",$item->id)}}">  {{isset($item->worker)? $item->worker->fullname():""}}</a>
                                        @else
                                            {{isset($item->worker)? $item->worker->fullname():""}}
                                        @endif
                                        @else
                                            @if($item->status_id !=4640301)
                                                <a href="{{route("hr.employment.admin.dashboard.view",$item->id)}}"> {{$item->company->caption}}</a>
                                            @else
                                                {{$item->company->caption??""}}
                                            @endif

                                        @endif
                                    </td>
                                    <td>{{ $item->personal_type->caption??"" }}</td>
                                    <td>{{ $item->cooperation_type->caption??"" }}</td>
                                    <td>{{ $item->national_code }}</td>
                                    <td>{{ $item->mobile }}</td>
                                    <td>{{ $item->get_status() }}</td>
                                    @if($item->status_id==4640118)
                                        <td>
                                                <a href="{{ route('hr.employment.admin.dashboard.letter',$item->id) }}"> <i class="fa fa-download"></i> نامه طب کار</a>
                                            </td>
                                    @else
                                        <td></td>
                                    @endif


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
            </div>

        </div>

        @endsection
        @section("styles")
            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
