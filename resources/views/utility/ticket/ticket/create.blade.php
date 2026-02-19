@extends('layouts.admin._master')

@section('page_header_title'," کارتابل تیکت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت تیکت جدید (<b>{{$ticket_type->caption}}</b>) </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.ticket.store",$ticket_type)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"موضوع  "])

                            @if($ticket_type->has_machine)
                                <div class="w-100"></div>
                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"machine_id",
                                        "label"=>" ماشین   ",
                                        "option"=>$machine_option["items"],
                                        "val"=>"",
                                        "text"=>"",
                                        "class_col"=>""
                                        ])
                                </div>
                            @endif
                            @if($ticket_type->has_ic)
                                <div class="w-100"></div>
                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"ic_id",
                                        "label"=>" مرکز هزینه   ",
                                        "option"=>$cost_center_option["items"],
                                        "val"=>"",
                                        "text"=>"",
                                        "class_col"=>""
                                        ])
                                </div>
                            @endif

                            <div class="col-md-10">
                                توضیحات

                               <textarea>
                                    <div id="editor" >
                                    <br/>
                                    <br/>
                                </div>
                               </textarea>
                            </div>
                        </div>
                        <div class="w-100"><br/><br/></div>
                        <a href="{{route("line_product_station.goods_kind.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت تیکت</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")


    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        .ck-content{
            text-align: right !important;
            direction:rtl !important;
        }
    </style>
@endsection

@section("scripts")
    <script src="https://cdn.ckeditor.com/ckeditor5/29.1.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .then( editor => {
                console.log( editor );
            } )
            .catch( error => {
                console.error( error );
            } );
        $('#form1').validate({
            rules: {
                "caption": "required",
                "machine_id_auto": "required",
                "ic_id_auto": "required",
            }
        });
    </script>
@endsection
