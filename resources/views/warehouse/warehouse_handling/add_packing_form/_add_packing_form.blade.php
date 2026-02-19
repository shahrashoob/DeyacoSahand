<div class="col-md-12 center">
    <form id="form_api">

        <h5>تعداد بسته بندی ثبت شده <span class="badge badge-secondary"
                                          id="sum_of_confirm_packing_form">{{$packing_form_count}}</span>
        </h5>
        <h5>آخرین بسته بندی خوانده شده <span class="badge badge-secondary"
                                             id="last_packing_form">---</span>
        </h5>
        <h5 class="text-danger">
            <span id="error_message"></span>
        </h5>

        <div  >
            <div class="" style="display: inline-block">
                کد بسته بندی
                <br/>
                <input id="packing_code" autofocus type="text"
                       style="width: 140px;height: 33px;margin-bottom: 15px"
                       value="">
            </div>
            @if($warehouse_handling->check_diff_in_weight || $warehouse_handling->check_diff_in_amount)
                <div class="" style="display: inline-block">
                    وزن ناخالص
                    <br/>

                    <input id="gross_weight" autofocus type="text" {{$smart_object?"disabled":""}}
                           style="width: 140px;height: 33px;margin-bottom: 15px"
                           value=""
                           onclick="set_id_for_smart_object('gross_weight');"
                    >
                </div>
            @endif
        </div>
        <br/>
        <button type="submit" class="btn btn-primary btn-sm" id="submit_packing_code"
                style="width: 140px">بررسی
            و
            ثبت
        </button>
    </form>
</div>

