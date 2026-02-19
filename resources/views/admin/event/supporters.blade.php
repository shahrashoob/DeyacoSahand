<!--  -->

<div class="timeline-row">
    <div class="timeline-icon">
        <div class="bg-danger-400">
            <i class="icon-files-empty2"></i>
        </div>
    </div>
    <div class="panel panel-flat timeline-content">
        <div class="panel-heading">
            <h5 class="panel-title">
                ليست افراد / شرکت های مرتبط
            </h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>

        <div class="panel-body">


            @if(count($event->supporters) > 0)
                <fieldset class="content-group">
                    <legend class="text-bold"> ليست اعضا/ شرکت ها</legend>

                    <table class="table table-bordered table-info">
                        <thead class="bold">
                        <tr>
                            <td>#</td>
                            <td>تصوير</td>
                            <td>نام و نام خانوادگی / نام شرکت</td>
                            <td>توضيحات كوتاه</td>
                            <td>نوع ارتباط</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody>
                        @php $number=1@endphp

                        @foreach($event->supporters as $item)
                            <tr>
                                <td>{{$number++}}</td>
                                <td><img style=" width:50px; height:50px" src="{{asset($item->image->path?? "sdfs")}}"
                                         alt=""></td>
                                <td> {{$item->caption}}</td>
                                <td> {{$item->description}}</td>
                                <td> {{$item->supporter_type->caption}}</td>

                                <td>

                                    <a href="{{url('panel/supporter/delete/'.$event->id."/".$item->id )}}"
                                       class="text-danger"> <i class="icon-trash"></i> </a>
                                    &nbsp;
                                    <a href="{{url('panel/supporter/edit/'.$event->id."/".$item->id )}}"
                                       class="text-edit"> <i class="icon-pencil"></i> </a>
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </fieldset>
            @endif

            @if(!isset($permission))
                <br/>
                <fieldset class="content-group">
                    <legend class="text-bold"> افزودن عضو جدید</legend>
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


                    <form class="form-horizontal" enctype="multipart/form-data" method="post"
                          action="{{url('panel/supporter/fileUpload/')}}">
                        {{ csrf_field() }}
                        <div class="row ">
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="name">نام و نام خانوادگی / نام شرکت:</label>
                                    <input type="text" class="form-control" name="caption" value="" required/>
                                </div>
                            </div>

                            <div class="col-md-8 " style="padding-right: 5px">
                                <div class="form-group">
                                    <label for="name">توضیحات :</label>
                                    <input type="text" class="form-control" name="description" value=""/>
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <label for="name">نوع ارتباط :</label>
                                <select class="form-control" id="supperter_type_id" name="supperter_type_id" required>
                                    <option value="">لطفا یک مورد انتخاب کنید</option>
                                    @foreach($supperter_types as $item)
                                        <option value='{{$item->id}}'>{{$item->caption}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-offset-8 col-md-4"><br/></div>
                            <div class="col-md-4">
                                <input type="file" name="image" class="btn btn-primary"/>
                                <input type="hidden" name="event_id" value="{{$event->id}}"/>
                                <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                            </div>
                            <div class="col-md-offset-8 col-md-4"><br/></div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="icon icon-plus2"></i> افزودن عضو
                                    جدید
                                </button>
                            </div>
                        </div>
                    </form>
                </fieldset>
            @endif

        </div>
    </div>

</div>
<!-- / -->
<script src="https://www.google.com/recaptcha/api.js?render={{config("recaptch.site_key")}}"></script>
<script>
    grecaptcha.ready(function () {
        grecaptcha.execute('{{config("recaptch.site_key")}}', {action: 'contact'}).then(function (token) {
            var recaptchaResponse = document.getElementById('recaptchaResponse');
            recaptchaResponse.value = token;
        });
    });
</script>
