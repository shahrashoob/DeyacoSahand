<div class="col-sm-12 ">
    <form id="form1" action="{{route("sales.setting.submit")}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf


        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th class="center">ردیف</th>

                    <th>رسته کالایی</th>
                    <th>مشخصه گروه بندی</th>
                </tr>

                </thead>
                <tbody>
                @php $row=1;@endphp
                @foreach($goods_kinds as $item)
                    <tr>
                        <td class="center">{{$row++}}</td>

                        <td>
                            {{$item->caption}}
                        </td>
                        <td class="center">
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"goods_kind_".$item->id,
                                    "option"=>$property_option[$item->id]["items"],
                                    "val"=>$property_option[$item->id]["value"],
                                    "text"=>$property_option[$item->id]["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                        </td>


                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

        <div class="col-md-12" style="text-align: center" id="button_list">
            <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>

            <button type="submit" class="btn btn-primary">
                ذخیره تغییرات
            </button>

        </div>
    </form>
</div>
