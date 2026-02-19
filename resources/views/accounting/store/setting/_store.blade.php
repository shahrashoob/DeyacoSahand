<form id="form1" action="{{route("accounting.store.setting.store")}}" method="post"
      enctype="multipart/form-data" novalidate="novalidate" autocomplete="false">
    @csrf
    <div class="row">

        <div class="table-responsive">
            <table class="table table-styling center">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th> عنوان کالا</th>
                    <th>قیمت</th>
                    <th>تاریخ اعتبار</th>
                </tr>

                </thead>
                <tbody>
                @php $row=0;@endphp
                @foreach($list as $item)
                    <tr>
                        <td style="vertical-align:middle">{{++$row}}</td>
                        <td style="vertical-align:middle">
                            <a >{{$item->caption?? ""}}</a>
                        </td>
                        <td>
                            @include("component.input._number", [
                                "id" => "price_{$item->id}",
                                'label' => "",
                                "value" => $item->price ?? "",
                                "class_col" => ""
                            ])
                        </td>
                        <td>
                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                                "id" => "validity_date_{$item->id}",
                                'label' => "",
                                "value" => $item->validity_date,
                                "class_col" => ""
                            ])
                        </td>

                    </tr>

                @endforeach
                </tbody>

            </table>
        </div>


    </div>
    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
