<!--  -->

<div class="timeline-row">
    <div class="timeline-icon">
        <div class="bg-success-400">
            <i class="icon-users2"></i>
        </div>
    </div>
    <div class="panel panel-flat timeline-content">
        <div class="panel-heading">
            <h5 class="panel-title">
                تصاوير رويداد            </h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <div class="thumb thumb-slide">
                        <img src="{{url($event->logo->path??"")}}" />
                    </div>
                </div>
            </div>
            <fieldset class="content-group">
                <legend class="text-bold"> تصویر اصلی رویداد</legend>
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>اخطار!</strong>مشکلی پیش آمده لطفا در انتخاب فایل دقت کنید.<br><br>
                        شما مجاز به آپلود انواع فایل های تصویری مي باشيد
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <form class="form-horizontal" enctype="multipart/form-data" method="post" action="{{url('panel/event/fileUpload/')}}">
                    {{ csrf_field() }}
                    <div class="row ">

                        <div class="col-md-4">
                            <input type="file" name="image"  class="btn btn-primary"/>
                            <input type="hidden" name="event_id" value="{{$event->id}}"/>
                            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                        </div>
                        <div class="col-md-offset-8 col-md-4"><br/></div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary"><i class="icon icon-upload4"></i> آپلود تصویر </button>
                        </div>
                    </div>
                </form>
            </fieldset>
<hr/>
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <div class="thumb thumb-slide">
                        <img src="{{url($event->cover->path??"")}}" />
                    </div>
                </div>
            </div>
            <fieldset class="content-group">
                <legend class="text-bold">  تصویر بنر رویداد - اندازه 213*350 </legend>
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>اخطار!</strong>مشکلی پیش آمده لطفا در انتخاب فایل دقت کنید.<br><br>
                        شما مجاز به آپلود انواع فایل های تصویری مي باشيد
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <form class="form-horizontal" enctype="multipart/form-data" method="post" action="{{url('panel/event/fileUploadBanner/')}}">
                    {{ csrf_field() }}
                    <div class="row ">

                        <div class="col-md-4">
                            <input type="file" name="image"  class="btn btn-primary"/>
                            <input type="hidden" name="event_id" value="{{$event->id}}"/>
                            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                        </div>
                        <div class="col-md-offset-8 col-md-4"><br/></div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary"><i class="icon icon-upload4"></i> آپلود تصویر </button>
                        </div>
                    </div>
                </form>
            </fieldset>




        </div>
    </div>

</div>
<!-- / -->
<script src="https://www.google.com/recaptcha/api.js?render={{config("recaptch.site_key")}}"></script>
<script>
    grecaptcha.ready(function () {
        grecaptcha.execute('{{config("recaptch.site_key")}}', { action: 'contact' }).then(function (token) {
            var recaptchaResponse = document.getElementById('recaptchaResponse');
            recaptchaResponse.value = token;
        });
    });
</script>
