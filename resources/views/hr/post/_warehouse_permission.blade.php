
<div class="row">
    <div class="col-md-6">

        <div class="card">
            <div class="card-header">
                <h5> مدیریت دسرسی به انبار</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>
                                <input type="checkbox" id="select_all_warehouse">
                                دسترسی به انبار
                            </th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($warehouse_list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>

                                    <input class="myCheckBox_warehouse" type="checkbox"  name="data[warehouse][{{$item->id}}]" {{$post->has_warehouse_permission($item->id)?"checked='checked'":""}}">
                                    <b>{{$item->fullCaption()}}</b>
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
    <div class="col-md-6">

        <div class="card">
            <div class="card-header">
                <h5> مدیریت دسرسی به  وضعیت فرم های ورود به انبار <br/> (داشبورد  ورود به انبار)</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>
                                <input type="checkbox" id="select_all_warehouse_operation">
                                دسترسی به وضعیت</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($warehouse_form_status_list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>

                                    <input  class="myCheckBox_warehouse_operation" type="checkbox"  name="data[warehouse_form_status][{{$item->id}}]" {{$post->has_order_status_permission($item->id)?"checked='checked'":""}}">
                                    <b>{{$item->caption}}</b>
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
    <div class="col-md-6">

        <div class="card">
            <div class="card-header">
                <h5> مدیریت دسرسی به  وضعیت فرم درخواست کالا از انبار <br/>(داشبورد خروج از انبار)</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>
                                <input type="checkbox" id="select_all_product_request">
                                دسترسی به وضعیت</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($product_request_form_status_list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>

                                    <input  class="myCheckBox_product_request" type="checkbox"  name="data[warehouse_form_status][{{$item->id}}]" {{$post->has_order_status_permission($item->id)?"checked='checked'":""}}">
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
</div>

