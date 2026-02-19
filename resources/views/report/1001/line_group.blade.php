@extends('layouts.admin._master')
@section("page_header_title","داشبور جاری تولید")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5> گزارش 1001 - در انتظار تولید ( به تفکیک گروه محصول) - {{$lineGroup->caption}}</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center; font-size: 11px !important;">
                            <thead>
                            <tr style="text-align: center">
                                <th colspan="3"></th>
                                <th colspan="4">تعداد در انتظار تولید</th>
                                <th colspan="4">وزن در انتظار تولید (کیلوگرم)</th>
                            </tr>
                            <tr>
                                <td>#</td>
                                <th>کد محصول</th>
                                <th style="max-width: 50px;">نام محصول</th>
                                <td>تعداد کارت ها</td>
                                <td>زیر 3 روز</td>
                                <td> 4-7 روز</td>
                                <td> 8 روز و بالاتر</td>
                                <td>تعداد کل</td>
                                <td> زیر 3 روز</td>
                                <td> 4-7 روز</td>
                                <td> 8 روز و بالاتر</td>
                                <td>وزن کل</td>
                            </tr>

                            </thead>
                            <tbody>
                            @php
                                $row=1;
                                $sum_count_production_cards=0;
                                $sum_list_0_3_count=0;
                                $sum_list_4_7_count=0;
                                $sum_list_8_100_count=0;
                                $sum_number=0;
                                $sum_list_0_3_weight=0;
                                $sum_list_4_7_weight=0;
                                $sum_list_8_100_weight=0;
                                $sum_weight=0;
                            @endphp
                            @foreach($list1 as $item)


                                @php
                                    $color="";
                                    $all=$item->sum_weight;
                                    $wieght_0_3=$list_0_3_weight[$item->product_id]??0;
                                    $persent=1;
                                    if($all!=0)
                                        $persent=$wieght_0_3 / $all;
                                    if($persent >= 1){
                                        $color="#6EB152";
                                    }
                                    elseif($persent >=.9 ){
                                        $color="#F5EB4B";
                                    }
                                    elseif($persent >=.8){
                                        $color="#E09327";
                                    }
                                    elseif($persent >=.7 ){
                                        $color="#D35932";
                                    }
                                    else{
                                        $color="#A42431";
                                    }


                                $sum_count_production_cards+=$item->count_production_cards;

                                $sum_list_0_3_count+=round($list_0_3_count[$item->product_id]??"0");
                                $sum_list_4_7_count+=round($list_4_7_count[$item->product_id]??"0");
                                $sum_list_8_100_count+=round($list_8_100_count[$item->product_id]??"0");
                                $sum_number+=round($item->sum_number);

                                $sum_list_0_3_weight+=round($list_0_3_weight[$item->product_id]??"0");
                                $sum_list_4_7_weight+=round($list_4_7_weight[$item->product_id]??"0");
                                $sum_list_8_100_weight+=round($list_8_100_weight[$item->product_id]??"0");
                                $sum_weight+=round($item->sum_weight);

                                @endphp
                                <tr style="background: {{$color}}; text-align: center;color:#0b0b0b">
                                    <td>{{$row++}}</td>
                                    <td>{{$item->code}}</td>
                                    <td>
                                        <a style="color: #0b0b0b; text-decoration: underline;"
                                           href="{{route("report.1001.product",$item->product_id)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>{{$item->count_production_cards}}</td>
                                    <td>{{round($list_0_3_count[$item->product_id]??"0")}}</td>
                                    <td>{{round($list_4_7_count[$item->product_id]??"0")}}</td>
                                    <td>{{round($list_8_100_count[$item->product_id]??"0")}}</td>
                                    <td>{{round($item->sum_number)}}</td>

                                    <td>{{round($list_0_3_weight[$item->product_id]??"0")}}</td>
                                    <td>{{round($list_4_7_weight[$item->product_id]??"0")}}</td>
                                    <td>{{round($list_8_100_weight[$item->product_id]??"0")}}</td>
                                    <td>{{round($item->sum_weight)}}</td>


                                </tr>
                            @endforeach
                            <tr style=" text-align: center;font-size: 16px; font-weight: bold">
                                <td colspan="3"> جمع </td>

                                <td>{{$sum_count_production_cards}}</td>
                                <td>{{$sum_list_0_3_count}}</td>
                                <td>{{$sum_list_4_7_count}}</td>
                                <td>{{$sum_list_8_100_count}}</td>
                                <td>{{$sum_number}}</td>

                                <td>{{$sum_list_0_3_weight}}</td>
                                <td>{{$sum_list_4_7_weight}}</td>
                                <td>{{$sum_list_8_100_weight}}</td>
                                <td>{{$sum_weight}}</td>


                            </tr>
                            </tbody>

                        </table>
                    </div>


                </div>

            </div>
        </div>
        <div class="col-md-12" style="text-align: center">
            <a href="{{route("report.1001.index")}}" class="btn btn-dark"> بازگشت</a>

        </div>

    </div>

@endsection

