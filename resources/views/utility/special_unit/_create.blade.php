<div class="card">
    <div class="card-header">
        <h5>افزودن واحد  خاص جدید
        </h5>
    </div>
    <form id="form2" action="{{route("utility.special_unit.store")}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <div class="card-block">

            <div class="row">
                @include("component.input._text",["id"=>"caption","label"=>"عنوان واحد اختصاصی"])

            </div>
            <button type="submit" class="btn btn-success"> افزودن  </button>
            <a class="btn btn-dark" href="{{route("line_product_station.goods_kind.property.index",$goods_kind)}}"> بازگشت به مشخصه های خاص </a>

        </div>
    </form>

</div>
