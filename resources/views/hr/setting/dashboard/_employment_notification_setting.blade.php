<form id="form1" action="{{ route("hr.setting.dashboard.update_employment_notification") }}" method="post" novalidate="novalidate">
    @csrf
<div class="row">

    <div class="col-sm-12">


        <div class="table-responsive">
            <table class="table table-styling">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th>وضعیت</th>
                    <th>پست جهت اطلاع رسانی</th>
                </tr>

                </thead>
                <tbody>
                @php $row=1;@endphp
                @foreach($status_list as $item)
                    <tr>
                        <td>{{$row++}}</td>
                        <td>
                            {{$item->id}} - {{$item->caption}}
                        </td>
                        <td>
                            <div class="col-md-12">
                                @include("component.input._select_simple",[
                                         "id"=>"post_id_".$item->id,
                                         "option"=>$post_option_list[$item->id]["items"],
                                         "val"=>$post_option_list[$item->id]["value"],
                                         "text"=>$post_option_list[$item->id]["text"],
                                         "class_col"=>""
                                         ])
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>


    </div>
    <div class="col-md-12">
        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
    </div>
</div>
</form>