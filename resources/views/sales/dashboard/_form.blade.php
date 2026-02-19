<form id="form1" style="display: inline" action="" method="post" novalidate="novalidate">
    @csrf


    <div class="col-xs-12">
        @if($reject_type==4)
            @include("component.input._textarea",["id"=>"comment","label"=>"توضیحات برای همکاران:"])
            @include("component.input._textarea",["id"=>"customer_comment","label"=>"توضیحات برای مشتری:"])

        @elseif($reject_type == 100)
            @include("component.input._lable",["label"=>"آخرین تخفیف خاص اعمال شده برای فاکتور ","value"=>$order->special_off_price,"class_col"=>"col-md-12"])
            @include("component.input._number",["id"=>"special_off_price","label"=>"مبلغ تخفیف خاص"])
        @elseif($reject_type == 120)
            <h5>آیا از ثبت مجوز بارگیری اطمینان دارید؟</h5>
        @elseif($reject_type == 130)
            @include("component.input._textarea",["id"=>"message","label"=>"لطفا دلیل خاتمه یافته کردن سفارش (برای همکاران) را وارد نمایید."])
            @include("component.input._textarea",["id"=>"customer_message","label"=>"لطفا دلیل خاتمه یافته کردن سفارش (برای مشتری) را وارد نمایید."])
 @else
            @include("component.input._textarea",["id"=>"message","label"=>"لطفا دلیل عدم تایید (برای همکاران) را وارد نمایید."])
            @include("component.input._textarea",["id"=>"customer_message","label"=>"لطفا دلیل عدم تایید (برای مشتری) را وارد نمایید."])

        @endif
    </div>
    <br/>
    @include("component.input._hidden",["id"=>'reject_type',"value"=>$reject_type])
    <div class="col-md-12">

        @if($reject_type==4)
            <input type="submit" class="btn btn-primary float-left " value="ثبت و ادامه"/>
        @elseif($reject_type == 100)
            <input type="submit" class="btn btn-primary float-left " value="ثبت تخفیف "/>
        @elseif($reject_type == 120)
            <input type="submit" class="btn btn-primary float-left " value="تایید مجوز بارگیری  "/>

        @elseif($reject_type == 5)
            <input type="submit" class="btn btn-danger float-left " value="ثبت و ادامه"/>

        @else
            <input type="submit" class="btn btn-primary float-left " value="ثبت و ادامه"/>

        @endif
        <a class="btn  md-close btn-outline-dark ">انصراف </a>
    </div>

</form>
