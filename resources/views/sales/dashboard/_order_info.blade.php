<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>مشخصات سفارش</h5>
        </div>
        <div class="card-block">
            <div class="row">
                @include("component.input._lable",["id"=>"","lable"=>" کد سفارش  ","value"=>$order->code(),"class_col"=>"col-md-3"])


                @include("component.input._lable",["id"=>"","lable"=>" کانال توزیع ",
                                "value"=>$order->customer->channelType->caption??"","class_col"=>"col-md-3"])

                @include("component.input._lable",["id"=>"","lable"=>" نام مرکز  ",
                                "value"=>$order->customer->caption??"","class_col"=>"col-md-3"])

                @include("component.input._lable",["id"=>"","lable"=>" کد مرکز  ",
                                "value"=>$order->customer->code??"","class_col"=>"col-md-3"])



                @include("component.input._lable",["id"=>"","lable"=>" تاریخ ایجاد پیش نویس   ",
                                "value"=>$order->create_datetime(),"class_col"=>"col-md-3"])

                @include("component.input._lable",["id"=>"","lable"=>" تاریخ ثبت درخواست   ",
                                "value"=>$order->order_datetime(),"class_col"=>"col-md-3"])


                @include("component.input._lable",["id"=>"prepayment_amount", "lable"=>"تاریخ تحویل","value"=>$order->delivery_datetime(),"class_col"=>"col-md-3"])

                @include("component.input._lable",["id"=>"","lable"=>" اولویت سفارش   ",
                                "value"=>$order->priority->caption??"","class_col"=>"col-md-3"])


                @include("component.input._lable",["id"=>"","lable"=>" تعداد روز در انتظار    ",
                "value"=>$order->number_of_days_waiting()??"","class_col"=>"col-md-3"])

                @include("component.input._lable",["id"=>"","lable"=>" مجوز بارگیری   ",
                "value"=>$order->loading_date()??"","class_col"=>"col-md-3"])



                @include("component.input._lable",["id"=>"","lable"=>($order->unit->measurement??"مقدار")."  کل ",
                "value"=>($order->total??"0")." ".($order->unit->caption??"مقدار"),"class_col"=>"col-md-3"])

                @include("component.input._lable",["id"=>"","lable"=>($order->unit->measurement??"مقدار")."  بار ارسال نشده     ",
                "value"=>($order->total_remaining??"0")." ".($order->unit->caption??"مقدار"),"class_col"=>"col-md-3"])


                @include("component.input._lable",["id"=>"","lable"=>" وزن کل     ",
                "value"=>($order->total_weight??"0")." کیلوگرم","class_col"=>"col-md-3"])


                @include("component.input._lable",["id"=>"","lable"=>" وزن بار ارسال نشده     ",
                "value"=>($order->total_weight_remaining??"0")." کیلوگرم","class_col"=>"col-md-3"])


                @if($post_user->checkButtonPermission("sales.view_leads_in_warehouse"))
                    @include("component.input._lable",["id"=>"","lable"=>" وزن بخشی از بار موجود در انبار     ",
                    "url"=>route("sales.dashboard.view_leads_in_warehouse",$order),
					"target"=>"",
                    "value"=>($order->total_weight_in_warehouse??"0")." کیلوگرم","class_col"=>"col-md-3"])
                @else
                    @include("component.input._lable",["id"=>"","lable"=>" وزن بخشی از بار موجود در انبار     ",
                    "value"=>($order->total_weight_in_warehouse??"0")." کیلوگرم","class_col"=>"col-md-3"])
                @endif
                <div class="w-100"></div>

                @include("component.input._lable",["id"=>"","lable"=>" نسبت وزنی ارسال شده     ",
                "value"=>"%".($order->total_weight_sent_raito??"0" ),"class_col"=>"col-md-3"])

                @include("component.input._lable",["id"=>"","lable"=>" نسبت وزنی موجود در انبار     ",
                "value"=>"%".($order->total_weight_in_warehouse_raito??"0" ),"class_col"=>"col-md-3"])


                <div class="w-100"></div>

                @include("component.input._lable",["id"=>"","lable"=>" وضعیت    ",
                "value"=>$order->getStatus(1)??"","class_col"=>"col-md-4"])

                @if(isset($product_request_forms) && count($product_request_forms)>0)
                    <div class="col-md-4">
                        شماره فرم درخواست کالا از انبار:
                        <b>

                            @foreach ($product_request_forms as $prf_item)
                            <div style="display: inline">
                                <button  class="text-primary"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: none;border: none;">
                                    <i class="fa fa-print"></i> {{$prf_item->code}}
                                </button>
                                <div class="dropdown-menu center" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(163px, 210px, 0px);">
                                    <a class="dropdown-item" href="{{route("sales.dashboard.print_product_request_form",[$order,$prf_item->id,3,"print"])}}" onclick="return confirm('آیا از پرینت درخواست اطمینان دارید؟')"> پرینت A۵ </a>
                                    <a class="dropdown-item" href="{{route("sales.dashboard.print_product_request_form",[$order,$prf_item->id,4,"print"])}}" onclick="return confirm('آیا از پرینت درخواست اطمینان دارید؟')"> پرینت A۴ </a>
                                    <a class="dropdown-item" href="{{route("sales.dashboard.print_product_request_form",[$order,$prf_item->id,3,"download"])}}" > دانلود A5 </a>
                                    <a class="dropdown-item" href="{{route("sales.dashboard.print_product_request_form",[$order,$prf_item->id,4,"download"])}}" >  دانلود A4 </a>


                                </div>
                            </div>
                            @endforeach

                        </b>
                      </div>

                @endif


                @include("component.input._lable",["id"=>"","lable"=>" شرح برگه    ",
                "value"=>$order->description_sheet->text??""])

            </div>
        </div>
    </div>
</div>
