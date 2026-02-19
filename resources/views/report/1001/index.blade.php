@extends('layouts.admin._master')
@section("page_header_title","داشبور جاری تولید")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5> گزارش 1001 - در انتظار تولید ( به تفکیک گروه محصولی)</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr style="text-align: center">
                                <th>#</th>
                                <th>نام گروه کالایی</th>
                                <th>وزن زیر 3 روز</th>
                                <th>وزن بین 4-7 روز</th>
                                <th>وزن بیش از 8 روز</th>
                                <th>وزن کل</th>
                                <th>تعداد کل کارت ها</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php
                                $row=1;
                                $sum_0_3=0;
                                $sum_4_7=0;
                                $sum_8_100=0;
                                $sum_weight=0;
                                $sum_production=0;@endphp
                            @foreach($line_groups as $key=>$item)
                                @php
                                    $color="";

                                    $all=$weights[$key]??1;
                                    $all=$all==0?1:$all;
                                    $wieght_0_3=$list_0_3[$key]??0;

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
                                    if(!isset($weights[$key]) ){
                                        $color="";
                                    }
                                $sum_0_3+=round($list_0_3[$key]??"0");
                                $sum_4_7+=round($list_4_7[$key]??"0");
                                $sum_8_100+=round($list_8_100[$key]??"0");
                                $sum_weight+=round($weights[$key]??"0");
                                $sum_production+=($production_cards[$key]??"0");
                                @endphp
                                <tr style="background: {{$color}}; text-align: center;color:#0b0b0b">
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a style="color: #0b0b0b; text-decoration: underline;"
                                           href="{{route("report.1001.line_group",$key)}}">{{$item}}</a>
                                    </td>
                                    <td>{{round($list_0_3[$key]??"0")}}</td>
                                    <td>{{round($list_4_7[$key]??"0")}}</td>
                                    <td>{{round($list_8_100[$key]??"0")}}</td>
                                    <td>{{round($weights[$key]??"0")}} </td>
                                    <td>{{($production_cards[$key]??"0")}}</td>

                                </tr>
                            @endforeach

                            <tr style=" text-align: center;font-size: 16px; font-weight: bold">
                                <td colspan="2"> جمع</td>
                                <td>{{$sum_0_3}}</td>
                                <td>{{$sum_4_7}}</td>
                                <td>{{$sum_8_100}}</td>
                                <td>{{$sum_weight}} </td>
                                <td>{{$sum_production}}</td>

                            </tr>
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>
        </div>

    </div>

@endsection
