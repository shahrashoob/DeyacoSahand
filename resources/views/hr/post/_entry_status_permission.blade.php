<div class="col-md-6">
    حداکثر تعداد دستگاه مجاز جهت اتصال به سامانه
    <input name="max_device_allow_for_login" required="required"
           value="{{$post->max_device_allow_for_login}}" type="number" min=0 style="width: 50px"
    >
    دستگاه می باشد.
    (تعداد دستگاه نامحدود: -1)
    <br/>
</div>
<br/>
<div class="table-responsive">
    <table class="table table-styling">
        <thead>
        <tr>
            <th>#</th>
            <th></th>
            <th>
                <input type="checkbox" id="select_all_personal_status">
                اجازه مشاهده صفحه پرسنلی
            </th>
            <th>
                <input type="checkbox" id="select_all_entry_status">
                اجازه مشاهده منو ها
            </th>
            <th>
                <input type="checkbox" id="">
                فیلتر دسترسی ها با توجه به زمان گروه شیفت
            </th>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($entry_status_list as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>
                    <b> {{$item->id." - ".$item->caption}}</b>
                </td>
                <td>
                    <input class="personal_status" type="checkbox" data-id="{{$item->id}}"
                           id="personal_status_{{$item->id}}"
                           name="data[entry_status][personal][{{$item->id}}]" {{$post->has_post_entry_permission($item->id,"allow_show_personal_menu")?"checked='checked'":""}}
                    ">
                </td>
                <td>
                    <input class="entry_status" type="checkbox" data-id="{{$item->id}}" id="entry_status_{{$item->id}}"
                           name="data[entry_status][all_menu][{{$item->id}}]" {{$post->has_post_entry_permission($item->id,"allow_show_all_menu")?"checked='checked'":""}}
                    ">
                </td>

                <td>
                    <input class="allow_filter_menu" type="checkbox" data-id="{{$item->id}}"
                           id="filter_menu_{{$item->id}}"
                           name="data[entry_status][filter_menu][{{$item->id}}]" {{$post->has_post_entry_permission($item->id,"allow_filter_menu")?"checked='checked'":""}}
                    ">
                </td>

            </tr>
        @endforeach
        </tbody>

    </table>

</div>

<div class="col-md-12">
    @include("component.input._checkbox",["id"=>"the_worker_has_permission_to_entering_from_static_ip","label"=>"آیا شاغلین مشغول در پست از طریق ای پی ثابت می توانند به سامانه وارد شوند","checked"=>$post->the_worker_has_permission_to_entering_from_static_ip])
    @include("component.input._checkbox",["id"=>"has_start_remote_work","label"=>"آیا شاغلین مشغول در پست امکان ثبت  شروع دورکاری دارند","checked"=>$start_remote_work])
    @include("component.input._checkbox",["id"=>"has_end_remote_work","label"=>"آیا شاغلین مشغول در پست امکان ثبت پایان دورکاری دارند","checked"=>$end_remote_work])

</div>
<a href="{{route("hr.post.index")}}" class="btn btn-outline-defualt">بازگشت</a>
<button type="submit" class="btn btn-success"
        onclick="return confirm('آیا از ثبت دسترسی ها اطمینان دارید')"> ثبت دسترسی
</button>

<script>
    $(".personal_status").click(function () {

        personal_check($(this).data('id'))

    })
    $(".entry_status").click(function () {

        entry_status($(this).data('id'))

    })

    function personal_check(id) {

        if ($("#personal_status_" + id).is(":checked")) {
            $("#filter_menu_" + id).removeAttr("disabled")
            $("#entry_status_" + id).removeAttr("disabled")

        } else {
            $("#filter_menu_" + id).prop("disabled", "disabled")
            $("#entry_status_" + id).prop("disabled", "disabled")
        }
    }

    function entry_status(id) {
        if ($("#entry_status_" + id).is(":checked")) {
            $("#filter_menu_" + id).removeAttr("disabled")

        } else {
            $("#filter_menu_" + id).prop("disabled", "disabled")
        }
    }

    @foreach($entry_status_list as $item)
            personal_check({{$item->id}})
            entry_status({{$item->id}})
    @endforeach

</script>

