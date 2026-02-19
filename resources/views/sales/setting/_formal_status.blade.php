<div class="col-sm-12 ">
    <form id="form2" action="{{route("sales.setting.submit_formal_status")}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf

        <div class="alert alert-info">
            فقط فاکتور هایی که سفارش آنها در وضعیت های زیر است، برای فیلد "مجموع سفارش های رسمی/غیر رسمی" در نظر گرفته
            می شوند.
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th class="center">ردیف</th>

                    <th>وضعیت سفارش</th>
                    <th></th>
                </tr>

                </thead>
                <tbody>
                @php $row=1;@endphp
                @foreach($formal_status as $item)
                    <tr>
                        <td class="center">{{$row++}}</td>

                        <td>
                            <input type="checkbox" name="data[status][{{$item->id}}]" {{in_array($item->id,$sale_formal_status_list)?"checked":""}}>
                            {{$item->caption}}
                        </td>
                        <td class="center">
                            <div class="col-md-6">

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
