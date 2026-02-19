@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست پیش نیاز های تنظیم شده
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th> #</th>
                                <th> عنوان گزینش</th>
                                <th>آموزش پیش نیاز</th>


                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($post_selection_education as $item)
                                <tr>
                                    <td>{{++$row}}</td>


                                    <td>{{$item->selection->caption}}</td>
                                    <td>{{$item->education->caption}}</td>
                                    <td>
                                        <a href="{{route("hr.post.post_selection_education.destroy",[$post,$selection,$post_selection_setting,$item->id])}}"
                                           onclick="return confirm('آیا از حذف این  تنظیمات پیش نیاز اطمینان دارید؟')"><i
                                                class="fa fa-trash text-danger"></i> </a>
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
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن پیش نیاز جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route('hr.post.post_selection_education.store',[$post,$selection,$post_selection_setting])}}"
                          method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._aotocomplet2",[

                                "id"=>"education_id",
                                "label"=>" آموزش ",
                                "option"=> $education_option["items"],
                                ])
                        </div>

                        <a href="{{route('hr.post.employment.index',$post)}}" class="btn btn-outline-dark">بازگشت</a>


                        <button type="submit" class="btn btn-primary"> ذخیره تغیرات</button>

                    </form>

                </div>


            </div>
        </div>

    </div>

@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "selection_type_id_auto": "required",
            }
        });
    </script>
@endsection
