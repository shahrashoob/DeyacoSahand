@extends('layouts.admin._master',["no_persian"=>1])


@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ویرایش شرایط تحویل فاکتور</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("condition_item.update",$condition_item)}}" method="post" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._aotocomplet2",[
                           "id"=>"pre_factor_type_id",
                           "label"=>$pre_factor_type["label"],
                           "option"=>$pre_factor_type["items"],
                           "val"=>$pre_factor_type["value"],
                           "text"=>$pre_factor_type["text"],
                           ])
                            @include("component.input._aotocomplet2",[
                       "id"=>"condition_type_id",
                       "label"=>$condition_type["label"],
                       "option"=>$condition_type["items"],
                       "val"=>$condition_type["value"],
                       "text"=>$condition_type["text"],
                       ])
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    @include("component.input._textarea",["id"=>"caption","label"=>"عنوان  ","value"=>$condition_item->caption])
                                  </div>
                            </div>

                        </div>
                        <a href="{{route("condition_item.index")}}" class="btn btn-danger">بازگشت</a>
                        <button type="submit" class="btn btn-primary">افزودن </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection


@section("scripts")
    <script type="text/javascript">
        $('#form1').validate({
            rules: {
                pre_factor_type_id_auto: "required",
                condition_type_id_auto: "required",
                caption: "required"
            }
        });
        tinymce.init({
            selector: 'textarea',
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern',
            ],
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media',
            relative_urls: false,
            directionality:"rtl",
            file_browser_callback: function(field_name, url, type, win) {
                tinyMCE.activeEditor.windowManager.open({
                    file: '/file-manager/tinymce',
                    title: 'مدیریت فایل ',
                    width: window.innerWidth * 0.8,
                    height: window.innerHeight * 0.8,
                    resizable: 'yes',
                    close_previous: 'no',
                }, {
                    setUrl: function(url) {
                        win.document.getElementById(field_name).value = url;
                    },
                });
            },
        });
        document.addEventListener("DOMContentLoaded", function() {

            document.getElementById('button-image').addEventListener('click', (event) => {
                event.preventDefault();

                inputId = 'image_id';

                window.open('/file-manager/fm-button', 'fm', 'width=800,height=600');
            });

        });

        // input
        let inputId = '';

        // set file link
        function fmSetLink($url) {
            document.getElementById(inputId).value = $url;
        }
        setTimeout( function () {
            $(".tox-button, .mce-close").click()
        },2000);
        setTimeout( function () {
            $(".tox-button, .mce-close").click()
        },4000);
        setTimeout( function () {
            $(".tox-button, .mce-close").click()
        },8000);
    </script>
@endsection

@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <script src="https://cdn.tiny.cloud/1/wmrmz8378w1tqohs7r1o5ygycllxfsmp33ux5cmnj7band3c/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>

@endsection
