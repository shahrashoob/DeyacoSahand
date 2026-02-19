@extends('layouts.admin._master')

@section('page_header_title',"داشبورد بسته بندی  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دریافت کد لات در نرم افزار مالی</h5>
                </div>
                <div class="card-block">
                    <div class="alert alert-warning">
                        برای ثبت کالا در انبار لازم است تا کد لات در نرم افزار مالی را برای لات کالاهای زیر را وارد
                        نمایید.
                        <br/>
                        {!! $setting["delivery_to_warehouse_without_register_lot_number_text"] ->string_value!!}
                    </div>
                    <form id="form1"
                          action="{{route("fabric_raw.packing_form.delivery_to_warehouse.submit_nosa_code",$packing_form)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @foreach($packing_form->items as $item)
                                @if ( $item->lot_number->nosa_code == null)
                                    @include("component.input._text",["id"=>"nosa_code_".$item->id,"lable"=>" کد همبافت در نرم افزار مالی برای ".$item->product->code." - لات ".$item->lot_number->code])
                                @endif
                            @endforeach
                            <div class="col col-md-12">

                                <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
                                   class="btn btn-outline-dark">
                                    بازگشت
                                </a>
                                <button class="btn btn-primary"
                                        onclick="return confirm('آیا از ثبت کد اطمینان دارید؟')">
                                    ثبت کد
                                </button>
                            </div>


                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                @foreach($packing_form->items as $item)
                "nosa_code_{{$item->id}}": "required",
                @endforeach
            }
        })
    </script>
@endsection
