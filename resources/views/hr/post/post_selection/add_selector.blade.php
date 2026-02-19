@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>لیست گزینش کنندگان
                            برای
                            {{$selection->caption}}

                            ({{$post->caption}})
                        </h5>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th> ردیف</th>
                                    <th>پست/کمیته</th>
                                    <th>الزامی </th>
                                    <th></th>

                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($selection_selectors as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>{{$item->post_selection->caption??""}}{{$item->committee->caption??""}}</td>
                                        <td>@if($item->confirmation_is_required==1)
                                                <i class="fa fa-check"></i>
                                            @else
                                                <i class="fa fa-times"></i>
                                            @endif</td>


                                        <td>
                                            <a href="{{route("hr.post.post_selection.destroy_add_selector",[$item->id,$selection,$post])}}"
                                               onclick="return confirm('آیا از حذف گزینش کننده اطمینان دارید؟')"><i
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
                        <h5>افزودن گزینش کننده</h5>
                    </div>
                    <div class="card-block">

                        <form id="form1"
                              action="{{route("hr.post.post_selection.store_add_selector",[$selection,$post])}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                            <div class="row">

                                @include("component.input._aotocomplet2",[
                                         "id"=>"post_id",
                                          "label"=>"پست ",
                                          "option"=>$post_option["items"],
                                          ])

                                @include("component.input._aotocomplet2",[
                                          "id"=>"committee_id",
                                         "label"=>"کمیته",
                                          "option"=>$committee_option["items"],
                                           ])

                                @include("component.input._radio_box01",["id"=>"confirmation_is_required","label"=>"آیا رد و تایید گزینش برای گزینش کننده الزامی  می باشد","label0"=>"خیر","label1"=>"بله","value"=>0])
                            </div>

                            <a href="{{route('hr.post.employment.index',$post)}}" class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary">افزودن</button>

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
                rules: {}
            });
        </script>
    @endsection
