@extends('layouts.admin._master')
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("warehouse.pallet.dashboard._search_view",["route"=>"wh.pallet.dashboard.index"])
        </div>

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست پالت ها</h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>

                                <th>کد پالت</th>
                                <th>تاریخ و ساعت</th>
                                <th>تعداد بسته بندی</th>
                                <th> وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("wh.pallet.dashboard.view",[$item])}}">{{$item->getCodeNumber()}}</a>
                                    </td>
                                    <td>
                                        {{$item->get_created_datetime()}}
                                    </td>
                                    <td>
                                        {{$item_count=$item->items()->count()}}
                                    </td>
                                    <td>{{$item->status->caption}}</td>
                                    <td style="text-align: right">
                                        <button class="btn  dropdown-toggle text-primary" type="button"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                style="width: 140px"
                                                aria-expanded="false">چاپ و دانلود
                                        </button>
                                        <div class="dropdown-menu" style="text-align: center">

                                            <a class="dropdown-item"
                                               href="{{route("wh.pallet.dashboard.print",[$item,302])}}"
                                               id="create_new_pallet_and_print"> چاپ(لیبل) </a>
                                            <a class="dropdown-item"
                                               href="{{route("wh.pallet.dashboard.print",[$item,8])}}"
                                               id="create_new_pallet_and_print_a4"> چاپ(A4)</a>

                                            <a class="dropdown-item"
                                               href="{{route("wh.pallet.dashboard.download",[$item,302])}}"
                                               id="end_of_pallet">دانلود (لیبل)</a>
                                            <a class="dropdown-item"
                                               href="{{route("wh.pallet.dashboard.download",[$item,8])}}"
                                               id="end_of_pallet_a4">دانلود (A4)</a>
                                        </div>
                                        <a href="{{route("wh.pallet.add_packing_form.index",$item)}}">
                                            <i class="fa  feather icon-log-in fa-rotate-90"></i>
                                            افزود به پالت
                                        </a>

                                        @if($item_count>0)
                                            <a href="{{route("wh.pallet.remove_packing_form.index",$item)}}"
                                               class="text-danger">
                                                <i class="fa  feather icon-log-out fa-rotate-270   "></i>
                                                کسر از پالت
                                            </a>
                                            &nbsp;
                                        @endif
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
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
