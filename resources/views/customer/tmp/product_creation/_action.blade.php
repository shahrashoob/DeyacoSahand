{{--در انتظار ارسال کد رهگیری پستی--}}
@if( $product_creation_process->status_id == 5231004 )
    <div class="alert alert-success " style="text-align: right">

        {{__("please send the fabric sample along with the design request form to the address in the design form and enter the postal tracking code in the form below")}}

        <form id="form1"
              action="{{route("customer_group.tmp.product_creation.submit_post_tracking_code",$product_creation_process)}}"
              method="post"
              autocomplete="off"
              novalidate="novalidate">
            @csrf
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <input name="tracking_code" value="" type="text" style="display: inline; height: 33px">
                        <button type="submit" class="btn btn-primary btn-sm"
                                style="display: inline; margin-bottom: 0px">
                            {{__("register tracking code")}}
                        </button>
                    </div>
                </div>
                <div class="col-md-2">

                </div>
            </div>
        </form>
    </div>
@endif
<a href="{{route("dashboard")}}"
   class="btn btn-outline-dark">{{__("btn.back")}}</a>

@if( $product_creation_process->has_physical_sample )
    <a class="btn btn-primary"
       href="{{route("customer_group.tmp.product_creation.download",$product_creation_process)}}"
    >
        <i class="fa fa-download"></i> {{__("btn.download form")}}
    </a>
@endif


