@if(isset($shade_number))
    <div class="collapse col-md-12" id="lot_{{$shade_number->id}}_{{$id}}">


        <div class="alert " style="border: 1px solid #0b0b0b; border-radius: 10px">

            <div class="row">
                   <span class="col-md-12">
                       کد شید: <b> {{$shade_number->code}}</b>
                  </span>

                <span class="col-md-12">
                      تاریخ ایجاد: <b> {{$shade_number->get_create_date_and_time()}}</b>
                    </span>
                <span class="col-md-12">

                      کاربر ایجاد کننده: <b> {{isset($shade_number->worker)?$shade_number->worker->fullName():"---"}}</b>
                    </span>


            </div>
        </div>

    </div>
@else
    ***
@endif
