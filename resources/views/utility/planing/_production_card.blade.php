<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h5> برگ برنامه ریزی کارت تولید {{$production->serial()}}</h5>
        </div>
        <div class="card-block" style="overflow: auto">
            <table class="table">

                <tr>
                    <td>
                        سفارش
                    </td>
                    <td>
                        @if(isset($production->order_id) && $production->order_id!=0)
                            <a href="{{route("utility.planing.view_order",$production->order_id)}}">{{$production->order->code()}}
                                </a>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td> مشتری</td>
                    <td> {{$production->customer->caption??""}}</td>
                </tr>
                <tr>
                    <td> کالا</td>
                    <td> {{$production->product->code??""}} - {{$production->product->caption??""}}</td>
                </tr>
                <tr>
                    <td>شماره فراخوانی</td>
                    <td>{{$production->call_id??""}}</td>
                </tr>
                <tr>
                    <td>خط تولید</td>
                    <td>{{$production->line->caption??""}}</td>
                </tr>
                <tr>
                    <td>خط - محصول</td>
                    <td>{{$production->line_product->id??""}}</td>
                </tr>
                <tr>
                    <td>زمان ست آپ - دقیقه</td>
                    <td>{{$production->set_up_time??""}}</td>
                </tr>
                <tr>
                    <td>زمان دون تایم - دقیقه</td>
                    <td>{{$production->set_up_time??""}}</td>
                </tr>
                <tr>
                    <td>زمان مجاز بیکاری - دقیقه</td>
                    <td>{{$production->unemployment_time??""}}</td>
                </tr>
                <tr>
                    <td>تخصیص خط - دقیقه</td>
                    <td>{{$production->line_allocation??""}}</td>
                </tr>
                <tr>
                    <td>زمان تولید</td>
                    <td>{{$production->production_time??""}}</td>
                </tr>
                <tr>
                    <td> تعداد درخواست</td>
                    <td>{{$production->number??""}}</td>
                </tr>
                <tr>
                    <td> تعداد تولید شده</td>
                    <td>{{$production->number_product??""}}</td>
                </tr>
                <tr>
                    <td> تعداد  در کارتن</td>
                    <td>{{$production->number_in_carton??""}}</td>
                </tr>
                <tr>
                    <td>تعداد تولید تکی</td>
                    <td>{{$production->sub_number_product??""}}</td>
                </tr>
                <tr>
                    <td>سرعت تولید</td>
                    <td>{{$production->production_speed??""}}</td>
                </tr>
                <tr>
                    <td>شاخص عملکرد </td>
                    <td>{{$production->productivity_index??""}}</td>
                </tr>
                <tr>
                    <td>ورژن کارت تولید  </td>
                    <td>{{$production->version??""}}</td>
                </tr>
                <tr>
                    <td>وضعیت  </td>
                    <td>{{$production->getStatus()}}</td>
                </tr>
                <tr>
                    <td>اولویت </td>
                    <td>{{$production->prioriry->caption??""}}</td>
                </tr>
                <tr>
                    <td>تاریخ صدور کارت </td>
                    <td>{{$production->get_create_date_and_time()}} </td>
                </tr>
                <tr>
                    <td>درخواست مواد اولیه </td>
                    <td>{{$production->is_master_of_rfw}} </td>
                </tr>
                <tr>
                    <td>سرپرست تولید </td>
                    <td>{{isset($production->supervisor_worker)?$production->supervisor_worker->fullname():"---"}} </td>
                </tr>
            </table>

        </div>
    </div>
</div>
