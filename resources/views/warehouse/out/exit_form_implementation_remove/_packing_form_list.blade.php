@include("warehouse.out.exit_form_implementation._script")

<div class="card">
    <div class="card-header">
        <h5> فرم خروج از انبار </h5>
    </div>
    <div class="card-block" id="card-block">
        <h6>مشخصات انبار</h6>
        <hr>
        <div class="row">
            <div class="col-md-4">
                @include("component.input._select",[
                "id"=>"trans_kind_id",
                "label"=>" نوع تراکنش  ",
                "option"=>$trans_kind_option["items"],
                "val"=>$trans_kind_option["value"],
                "text"=>$trans_kind_option["text"],
                "class_col"=>""
                ])
            </div>
            <div class="col-md-4">
                @include("component.input._select",[
                "id"=>"cost_center_id",
                "label"=>" مرکز هزینه ",
                "option"=>$cost_center_option["items"],
                "val"=>$cost_center_option["value"],
                "text"=>$cost_center_option["text"],
                "class_col"=>""
                ])
            </div>

            <div class="col-md-4">
                @include("component.input._select",[
                "id"=>"opp_kind_id",
                "label"=>" طرف حساب ",
                "option"=>$opp_kind_option["items"],
                "val"=>$opp_kind_option["value"],
                "text"=>$opp_kind_option["text"],
                "class_col"=>""
                ])
            </div>
            @include("component.input._textarea",["label"=>"شرح تراکنش","id"=>"description","value"=>$description,"width"=>"", "height"=>"50px"])
        </div>
        <h6>مشخصات بسته بندی ها</h6>
        @include("component.input._hidden",["id"=>"packing_form_list_ids","value"=>json_encode($packing_form_list_ids)])
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive center">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th style="width: 10px">ردیف</th>
                            <th>کد بسته بندی</th>
                            <th>مقدار</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($packing_form_list as $packing_form)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>{{$packing_form->code}}</td>
                                <td>{{$packing_form->getFinalAmount()}}</td>
                                <td></td>
                            </tr>
                        @endforeach


                        </tbody>
                    </table>
                </div>


            </div>

        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5> انتخاب بسته بندی (ها) </h5>
    </div>
    <div class="card-block" id="card-block">
        <div class="row">
            <div class="col-md-12">
                @if(isset($error) && $error!="" )
                <div class="alert alert-danger">{!! $error !!}</div>
                @endif
                <div class="table-responsive center">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th style="width: 10px">ردیف</th>
                            <th> انتخاب یک  بسته بندی</th>
                            <th> انتخاب چند  بسته بندی</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        <tr>
                            <td style="font-size: 16px; padding-top: 25px">{{++$row}}</td>

                            <td>

                                <input id="packing_form_code" type="number" style="width: 140px" value="" autofocus >
                                /DCPK

                                <br/>
                                <br/>
                                <a href="#sfdf" class="submit_packing_code btn btn-primary btn-sm" >بررسی و
                                    ثبت
                                </a>

                            </td>
                            <td>
                                از کد
                                <input id="from_packing_form_code" type="number" style="width: 140px" value="">
                                /DCPK

                                تا کد
                                <input id="to_packing_form_code" type="number" style="width: 140px" value="">
                                /DCPK
                                <br/>
                                <br/>
                                <a href="#sfdf" class="submit_packing_code btn btn-primary btn-sm" >بررسی و
                                    ثبت
                                </a>
                            </td>

                        </tr>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
    @if(count($packing_form_list) > 0)
        <div class="col-md-12 center">
            <button type="submit" class="btn btn-primary"> ثبت فرم خروج</button>
        </div>
@endif
<script>
    $("#packing_form_code").focus();
</script>


