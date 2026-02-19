@extends('layouts.admin._master')
@section("page_header_title","کارتابل منابع انسانی")
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>مدارک مورد نیاز</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route('hr.employment.admin.personal.delivery_of_document.submit',$employment)}}"
                          method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <p>لطفاً مدارک زیر را از کارمند دریافت نموده و پس از دریافت مدارک، مدارک موردنیاز را انتخاب نمایید و سپس تایید کنید.</p>
                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>عنوان</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $row=0; @endphp
                                @foreach($document_receive_step_document_types as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            <input class="Checkbox" type="checkbox" name="is_deliver_document_ok[{{$item->id}}]">
                                            {{$item->document_type->caption}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-secondary" disabled>تایید</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('.Checkbox').change(function () {
            var count = $('.Checkbox:checked').length;
            var total = {{count($document_receive_step_document_types)}};
            $('button[type="submit"]').prop('disabled', count !== total).toggleClass('btn-primary', count === total).toggleClass('btn-secondary', count !== total);
        });
    </script>
@endsection
