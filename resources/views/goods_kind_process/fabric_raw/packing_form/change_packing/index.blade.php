@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بسته بندی")

@section('content')
    @php $product_unit=$packing_form->items[0]->product->unit; @endphp
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                        <h5> تغییر فرم بسته بندی {{$packing_form->getCode()}}   </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.packing_form.change_packing.submit",[$packing_form])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        <div class="w-100"></div>

                        <div class="table-responsive">
                            @include("component.input._lable",["id"=>"","lable"=>"شماره بسته بندی  ","value"=>$packing_form->code,"class_col"=>"col-md-12"])

                            @include("component.input._lable",["id"=>"","lable"=>" حامل","value"=>isset($packing_form->carrier)?$packing_form->carrier->code:"فاقد حامل"])

                            @include("component.input._lable",["id"=>"","lable"=>$product_unit->measurement. " سیستم ","value"=>$packing_form->getAmount()." ".$product_unit->caption,"class_col"=>"col-md-12"])



                            @foreach($packing_form->items()->orderBy("band_code")->orderByDesc("id")->get() as $item)
                                @include("component.input._number",[
                                    "id"=>"data[packing_form_item][".$item->id."]",
                                    "lable"=>"باند ".$item->band_code. ": ".$product_unit->measurement."  ".($item->product->fullCaption()).' - همبافت'.($item->lot_number->code??"***"),
                                    "value"=>"",
                                    "class_col"=>"col-md-3"])
                            @endforeach

                            <div class="col-md-12">
                                <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-success">تایید و ادامه</button>
                            </div>


                        </div>

                    </form>
                </div>

            </div>

        </div>
    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                @foreach($packing_form->items as $item)
                    "data[lot_number][{{$item->lot_number->id??0}}]": "required",
                @endforeach
            }
        });
    </script>
@endsection
