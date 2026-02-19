
@if($employment->worker->user_bank_accounts->count()>=1)
    <div class="col-md-12 center ">
    <div class="table-responsive">
        <table class="table table-styling">
            <thead>
            <tr>
                <th> ردیف</th>
                <th> نام بانک</th>
                <th>شعبه بانک</th>
                <th>شماره حساب</th>
                <th>شماره شبا</th>
      
                <th></th>

            </tr>
            </thead>
            <tbody>
            @php $row=0;@endphp
            @foreach($employment->worker->user_bank_accounts as $user_bank_account)
                <tr>
                    <td>{{++$row}}</td>
                    <td>{{$user_bank_account->bank->caption}}</td>
                    <td>{{$user_bank_account->bank_branch}}</td>
                    <td>{{$user_bank_account->account_number}}</td>
                    <td>{{$user_bank_account->shaba_number}}</td>
                    <td>
                        @if($allow_delete)
                        <a href="{{route("hr.employment.register.personal.bank_information.destroy",[$employment->key,$user_bank_account->id])}}"
                           onclick="return confirm('آیا از حذف اطلاعات بانکی خود اطمینان دارید؟')"><i
                                class="fa fa-trash text-danger"></i> </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    </div><br/>
@else
    <p class=" alert alert-warning">هیچ اطلاعاتی ثبت نشده است.</p>
@endif

