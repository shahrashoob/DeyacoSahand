<form id="form1" action="{{ route("hr.setting.dashboard.update_bank") }}" method="post" novalidate="novalidate">
    @csrf
    <div class="row">
        <div class="col-md-12">
            لیست بانک ها:
            <br/>
            <div class="row">
                @foreach($bank_list as $item)
                    <div class="col-md-6">
                        <input type="checkbox" name="is_bank_allowed_to_choose[{{$item->id}}]" {{($item->is_bank_allowed_to_choose==1)?"checked='checked'":""}}>
                        {{$item->caption}}
                    </div>
                @endforeach
            </div>
            <br/>
            <br/>
        </div>

        <div class="col-md-12">
            <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
        </div>
    </div>
</form>
