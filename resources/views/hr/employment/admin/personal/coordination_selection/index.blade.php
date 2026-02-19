@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')

    <div class="row">
        <div class="col-sm-12">
            @include('component.input.datepicker._script')
            @include('component.input.datepicker.jalali_datepicker._script')
            <form id="form1" action="{{route('hr.employment.admin.personal.coordination_selection.submit',$employment)}}"
                  method="post"
                  enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                @csrf
                @foreach( $employment_selections as $item)
                    <div class="card">
                        <div class="card-header">
                            <h5>هماهنگی ساعت و تاریخ
                                {{$item->selection->caption}}
                                (
                                @foreach($item->employment_selection_selectors as $item_selectors)
                                    @if($item_selectors->committee)
                                        {{$item_selectors->committee->caption}}
                                    @else
                                        {{$item_selectors->post->caption}}
                                    @endif
                                @endforeach
                                )
                            </h5>
                        </div>
                        <div class="card-block">
                            <p>
                                <b>{{$worker->fullname("with_gender_2")}}</b>
                                <br/>
                                لطفا زمان و ساعت
                                <b>{{$item->selection->caption}}</b>
                                با
                                <b>{{$item_selectors->post->caption}}</b>
                                و
                                <b>{{$employment->worker->fullname("with_gender_2")}}</b>
                                را در فرم زیر تکمیل کنید.
                            </p>


                            <div class="row">
                                @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                                         "id"=>"coordination_time_". $item->id ,
                                          "label"=>"تاریخ وساعت هماهنگی",
                                          "class"=>"form-control",
                                          "class_col"=>"col-md-3",
                                          "hasTime"=>1,
                                           "min_date"=>"today",
                                           "name"=>"coordination_time". $item->id ,
                                           ])
                                <div class="w-100"></div>
                                @include("component.input._textarea", ["id"=>"text_" . $item->id, 'label'=>"توضیحات",  "class_col"=>"col-md-6",])
                            </div>


                        </div>
                    </div>
                @endforeach
                <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}"
                   class="btn btn-outline-dark">بازگشت</a>

                <button type="submit" class="btn btn-primary">تایید</button>

            </form>

        </div>

    </div>

@endsection

@section("styles")
    @include("component.input.datepicker.jalali_datepicker._style")
    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    @include("component.input.datepicker.jalali_datepicker._script")
    <script>
        $('#form1').validate({
            rules: {
                "text": "required",
                "coordination_time_{{$current_employment_selection->id}}_value": "required",

            }
        });
    </script>
@endsection
