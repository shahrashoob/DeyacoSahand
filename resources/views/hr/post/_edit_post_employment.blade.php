<div class="row">
    <div class="col-md-6">

        <div class="card">
            <div class="card-header">
                <h5> دسترسی به وضعیت های درخواست  همکاری</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>
                                <input type="checkbox" id="select_all_employment">
                                دسترسی به درخواست
                            </th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($employment_status_list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>

                                    <input  class="myCheckBox_post_employment" type="checkbox"  name="data[post_employment_status][{{$item->id}}]" {{$post->has_order_status_permission($item->id)?"checked='checked'":""}}">
                                    <b>{{$item->id}} - {{$item->caption}}</b>
                                </td>
                                <td>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>
                <a href="{{route("hr.post.index")}}" class="btn btn-outline-defualt">بازگشت</a>
                <button type="submit" class="btn btn-success"
                        onclick="return confirm('آیا از ثبت دسترسی ها اطمینان دارید')"> ثبت دسترسی
                </button>

            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
                <h5> دسترسی به عملیات های درخواست همکاری </h5>

            </div>
            <div class="card-block">

                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <th></th>
                            <th>
                                <input type="checkbox" id="select_all_employment_operation">
                                انتخاب همه
                            </th>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($employment_button_list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>

                                        <input class="myCheckBox_employment_operation" type="checkbox"
                                               id="switch-data[{{$item->id}}]"
                                               name="data[employment_button][{{$item->id}}]" {{$post->has_button_permission($item->id) ?"checked='checked'":""}}
                                        >
                                        <b>{{$item->id}} - {{$item->caption}}</b>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>

                    </div>

                    <a href="{{route("hr.post.edit",$post)}}" class="btn btn-outline-defualt">بازگشت</a>
                    <button type="submit" class="btn btn-success"
                            onclick="return confirm('آیا از ثبت دسترسی ها اطمینان دارید')"> ثبت دسترسی
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
