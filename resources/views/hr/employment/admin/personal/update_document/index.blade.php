@extends('layouts.admin._master')
@section("page_header_title","کارتابل منابع انسانی")
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>بروز رسانی مدارک پرسنل</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route('hr.employment.admin.personal.update_document.submit',$employment)}}"
                          method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <p>لطفا مدارکی که نیاز به ویرایش دارند را انتخاب کرده و فایل جدید را بارگذاری نمایید.</p>
                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>نام مدرک</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $row=0; @endphp
                                @foreach($employment_document_types_for_personal as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            <a href="{{route("hr.employment.admin.confirm.upload_document.download",[$employment,$item->id])}}?{{rand(1,9999)}}">
                                                {{$item->document_type->caption}}  </a>
                                        </td>

                                        <td>
                                            <input  type="file" name="document_type_{{$item->id}}">
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary" >بارگذاری مدارک</button>
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
        $('#form1').validate({
            rules: {
                "caption": "required",
                "code": "required",
                "status_id_auto": "required",
            }
        });
    </script>
@endsection
