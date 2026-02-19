<div class="alert alert-info" style="overflow: auto">
    <table class="table table-styling center">
        <thead>

        <tr>

            <th>کد فرم</th>
            <th>تاریخ ایجاد</th>
            <th>کاربر ایجاد کننده فرم</th>
            <th>انبار</th>
            <th> وضعیت</th>
            <th></th>
        </tr>
        </thead>
        <tr>

            <td>
                <a href="{{route("DCEF_QR",[$search_exist_form_model->id,$search_exist_form_model->getRandom(),"wh.out.dashboard.index"])}}">
                    {{$search_exist_form_model->code}}
                </a>
            </td>
            <td>{{$search_exist_form_model->get_create_date_and_time()}}</td>
            <td>{{$search_exist_form_model->worker->fullname()}}</td>
            <td>{{$search_exist_form_model->warehouse->caption??""}}</td>
            <td> {{$search_exist_form_model->status->caption}}</td>
        </tr>
        <tr>
            <td colspan="5">
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-group mb-2 mr-2">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">دانلود برگ خروج به تفکیک کالا
                            </button>
                            <div class="dropdown-menu" x-placement="bottom-start"
                                 style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.download_with_out_request_form",[$search_exist_form_model,4,"product"])}}">A4</a>
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.download_with_out_request_form",[$search_exist_form_model,3,"product"])}}">A5</a>
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.download_with_out_request_form",[$search_exist_form_model,1,"product"])}}">95*123</a>
                            </div>
                        </div>

                        <div class="btn-group mb-2 mr-2">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">دانلود برگ خروج به تفکیک بسته بندی
                            </button>
                            <div class="dropdown-menu" x-placement="bottom-start"
                                 style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.download_with_out_request_form",[$search_exist_form_model,4,"packing_form"])}}">A4</a>
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.download_with_out_request_form",[$search_exist_form_model,3,"packing_form"])}}">A5</a>
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.download_with_out_request_form",[$search_exist_form_model,1,"product"])}}">95*123</a>
                            </div>
                        </div>
                        <br/>
                        <div class="btn-group mb-2 mr-2">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">پرینت برگ خروج به تفکیک کالا
                            </button>
                            <div class="dropdown-menu" x-placement="bottom-start"
                                 style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.print_with_out_request_form",[$search_exist_form_model,4,1,"product"])}}">A4</a>
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.print_with_out_request_form",[$search_exist_form_model,3,1,"product"])}}">A5</a>
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.print_with_out_request_form",[$search_exist_form_model,1,1,"product"])}}">95*123</a>
                            </div>
                        </div>

                        <div class="btn-group mb-2 mr-2">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">پرینت برگ خروج به تفکیک بسته بندی
                            </button>
                            <div class="dropdown-menu" x-placement="bottom-start"
                                 style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.print_with_out_request_form",[$search_exist_form_model,4,1,"packing_form"])}}">A4</a>
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.print_with_out_request_form",[$search_exist_form_model,3,1,"packing_form"])}}">A5</a>
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.print_with_out_request_form",[$search_exist_form_model,12,1,"product"])}}">A5 - فرمت 2</a>
                                <a class="dropdown-item"
                                   href="{{route("wh.out.exit_form.print_with_out_request_form",[$search_exist_form_model,1,1,"product"])}}">95*123</a>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

    </table>
</div>