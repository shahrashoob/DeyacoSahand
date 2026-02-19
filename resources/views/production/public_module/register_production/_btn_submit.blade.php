<a class="btn btn-primary spinner-remove" href="#!" id="register_print_continue">ثبت ، چاپ و
    ادامه</a>
<br/>
<br/>

    <div class="btn-group mb-2 mr-2">


        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                aria-haspopup="true"
                style="width: 140px"
                aria-expanded="false">پالت
        </button>
        <div class="dropdown-menu" style="text-align: center">
            <a class="dropdown-item" href="#!" id="create_new_pallet">پالت جدید</a>
            <a class="dropdown-item" href="#!" id="create_new_pallet_and_print">پایان پالت، چاپ(لیبل) و جدید</a>
            <a class="dropdown-item" href="#!" id="create_new_pallet_and_print_a4">پایان پالت، چاپ(A4) و جدید</a>

            <a class="dropdown-item" href="#!" id="end_of_pallet">پایان پالت و چاپ لیبل</a>
            <a class="dropdown-item" href="#!" id="end_of_pallet_a4">پایان پالت و چاپ (A4)</a>
        </div>
    </div>
    <div class="btn-group mb-2 mr-2">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                aria-haspopup="true"
                style="width: 140px"
                aria-expanded="false">ثبت
        </button>
        <div class="dropdown-menu" style="text-align: center">
            <a class="dropdown-item" href="#!" id="register_continue">ثبت و ادامه</a>
            <a class="dropdown-item" href="#!" id="register_back">ثبت و بازگشت</a>

            <a class="dropdown-item" href="#!" id="register_print_back">ثبت ، چاپ و
                بازگشت</a>
        </div>
    </div>
    <div class="btn-group mb-2 mr-2">
        <a class="btn btn-outline-dark" style="width: 140px"
           href="{{route("production.public_module.register_production.index",[$machine_allocation,$source_production_form_item_id])}}">

            بازگشت

        </a>
    </div>



</div>