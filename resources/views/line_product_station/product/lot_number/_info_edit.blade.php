<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5> ویرایش لات   {{$lot_number->caption}} </h5>
            </div>
            <div class="card-block">

                <form id="form1" action="{{route($route_path."update",[$product,$lot_number,$product_creation_process])}}" method="post"
                      autocomplete="off"
                      novalidate="novalidate">
                    @csrf
                    <div class="row">
                        @include("component.input._text",["id"=>"code",'label'=>"کد لات ","value"=>$lot_number->code])

                    </div>

                    <a href="{{route($route_path."index",$product_creation_process?$product_creation_process:$product)}}" class="btn btn-outline-dark">بازگشت</a>

                    <button type="submit" class="btn btn-primary">  ذخیره تغییرات </button>

                </form>

            </div>
        </div>
    </div>

</div>
