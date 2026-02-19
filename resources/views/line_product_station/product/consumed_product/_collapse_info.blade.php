<div class="collapse {{isset($show_collapse)?"show":""}} col-md-12" id="{{$id??"collapseExample"}}">


    <div class="alert " style="border: 1px solid #0b0b0b; border-radius: 10px;overflow: auto">

        <div class="row">

            <table class="table table-styling center">
                <tr>
                    <td>کد</td>
                    <td>نام کالای مصرفی</td>
                </tr>
                @foreach($product->consumed_product as $item)

                    <tr>
                        <td>{{$item->material->code??""}}</td>
                        <td>{{$item->material->caption??""}}</td>
                    </tr>

                @endforeach
            </table>
        </div>
    </div>

</div>