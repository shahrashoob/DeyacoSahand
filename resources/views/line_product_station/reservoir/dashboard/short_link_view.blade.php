@extends('layouts.admin._master')

@section('page_header_title',"داشبورد مدیریت  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>مخزن شماره {{$reservoir->id}} - {{$reservoir->caption}}</h5>
                </div>
                <div class="card-block">


                    <div class="row">


                        @include("component.input._lable",["id"=>"","lable"=>"  نام مخزن","value"=>$reservoir->caption])

                        @php $row=1; @endphp
                        کالای مجاز:
                        <br/>
                        <br/>
                        <div class="table-responsive center">
                            <table class="table table-styling">
                                <tr>
                                    <th>ردیف</th>
                                    <th>کد کالا</th>
                                    <th>نام کالا</th>
                                </tr>
                                @foreach($reservoir->products as $item)

                                    <tr>
                                        <td>{{$row}}</td>
                                        <td>
                                            {{$item->product->code}}


                                        </td>

                                        <td>
                                            {{$item->product->caption}}


                                        </td>
                                    </tr>

                                @endforeach

                            </table>
                        </div>
                    </div>


                    @include("line_product_station.reservoir.dashboard._action")


                </div>
            </div>
        </div>


        @include("line_product_station.reservoir.dashboard._item_list",["packing_form"=>$reservoir->packing_form])



        {{--        <div class="col-sm-12">--}}

        {{--            <div class="card">--}}
        {{--                <div class="card-header">--}}
        {{--                    <h5> سابقه عملیات بر مخزن }</h5>--}}
        {{--                </div>--}}
        {{--                <div class="card-block">--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}


    </div>

@endsection

@section("scripts")
    <script>

    </script>
@endsection
