@if(isset($lot_number))
    <div class="collapse col-md-12" id="lot_{{$lot_number->id}}_{{$id}}">


        <div class="alert " style="border: 1px solid #0b0b0b; border-radius: 10px">

            <div class="row">
                   <span class="col-md-12">
                       کد لات : <b> {{$lot_number->code}}</b>
                  </span>
                <span class="col-md-12">

                       کد لات در نرم افزار مالی: <b> {{$lot_number->nosa_code}}</b>
                </span>
                <span class="col-md-12">
                       کد موثر ماشین: <b> {{$lot_number->getMachineLotEffectiveCode()}}</b>
                </span>
                @if(isset($lot_number->lot_number_1))
                    <span class="col-md-12">
                      لات   {{$lot_number->lot_number_1->product->caption??"***"}} :<b> {{$lot_number->lot_number_1->code}}</b>
                </span>
                @endif

                @if(isset($lot_number->lot_number_2))
                    <span class="col-md-12">
                      لات   {{$lot_number->lot_number_2->product->caption??"***"}} : <b> {{$lot_number->lot_number_2->code}}</b>
                </span>
                @endif

                @if(isset($lot_number->lot_number_3))
                    <span class="col-md-12">
                       لات  {{$lot_number->lot_number_3->product->caption??"***"}} : <b> {{$lot_number->lot_number_3->code}}</b>
                </span>
                @endif

                @if(isset($lot_number->lot_number_4))
                    <span class="col-md-12">
                      لات   {{$lot_number->lot_number_4->product->caption??"***"}} : <b> {{$lot_number->lot_number_4->code}}</b>
                </span>
                @endif

                @if(isset($lot_number->lot_number_5))
                    <span class="col-md-12">
                      لات   {{$lot_number->lot_number_5->product->caption??"***"}} : <b> {{$lot_number->lot_number_5->code}}</b>
                </span>
                @endif

                @if(isset($lot_number->lot_number_6))
                    <span class="col-md-12">
                      لات   {{$lot_number->lot_number_6->product->caption??"***"}} : <b> {{$lot_number->lot_number_6->code}}</b>
                </span>
                @endif

                @if(isset($lot_number->lot_number_7))
                    <span class="col-md-12">
                      لات   {{$lot_number->lot_number_7->product->caption??"***"}} : <b> {{$lot_number->lot_number_7->code}}</b>
                </span>
                @endif

                @if(isset($lot_number->lot_number_8))
                    <span class="col-md-12">
                      لات   {{$lot_number->lot_number_8->product->caption??"***"}} : <b> {{$lot_number->lot_number_8->code}}</b>
                </span>
                @endif

                @if(isset($lot_number->lot_number_9))
                    <span class="col-md-12">
                      لات   {{$lot_number->lot_number_9->product->caption??"***"}} : <b> {{$lot_number->lot_number_9->code}}</b>
                </span>
                @endif

                @if(isset($lot_number->lot_number_10))
                    <span class="col-md-12">
                      لات   {{$lot_number->lot_number_10->product->caption??"***"}} : <b> {{$lot_number->lot_number_10->code}}</b>
                </span>
                @endif

                <span class="col-md-12">
                      تاریخ ایجاد: <b> {{$lot_number->get_create_date_and_time()}}</b>
                    </span>
                <span class="col-md-12">

                      کاربر ایجاد کننده: <b> {{isset($lot_number->worker)?$lot_number->worker->fullName():"---"}}</b>
                    </span>
                <span class="col-md-12">

                      ماشین ایجاد کننده : <b> {{isset($lot_number->machine)?$lot_number->machine->fullCaption():"---"}}</b>
                </span>


            </div>
        </div>

    </div>
@else
    ***
@endif
