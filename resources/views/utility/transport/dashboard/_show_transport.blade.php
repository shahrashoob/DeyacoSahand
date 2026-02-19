<div class="row">

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5> بار شماره: {{$transport->getCode()}} </h5>
            </div>

            <div class="card-block">

                @include("utility.transport.public._transport_info")

            </div>
        </div>
    </div>


    <div class="col-md-12 center">

        @if(isset($route_url))
            <a href="{{$route_url}}" class="btn btn-outline-dark">بازگشت</a>
        @endif

        @if ( $transport->status_id == 6010104 && $post_user->checkButtonPermission( "guarding.dashboard.allow_confirm_input_loading" ))
            <form id="form1" autocomplete="off"
                  action="{{route("utility.transport.dashboard.confirm_input",[$transport])}}"
                  method="post"
                  novalidate="novalidate"
                  style="display: inline"
            >
                @csrf
                <button type="submit" class="btn btn-primary"
                        onclick="return confirm('آیا از تایید ورود بار اطمینان دارید؟')">ثبت ورود
                    (نگهبانی)
                </button>

                <a href="{{route("utility.transport.dashboard.reject_input",[$transport])}}" type="submit"
                   class="btn btn-danger"
                   onclick="return confirm('آیا از عدم تایید ورود بار اطمینان دارید؟')">عدم تایید
                </a>
            </form>
        @endif


            @if ( $transport->status_id == 6010101 && $post_user->checkButtonPermission( "guarding.dashboard.allow_confirm_output_loading" ))
                <form id="form1" autocomplete="off"
                      action="{{route("utility.transport.dashboard.confirm_output",[$transport])}}"
                      method="post"
                      novalidate="novalidate"
                      style="display: inline"
                >
                    @csrf
                    <button type="submit" class="btn btn-primary"
                            onclick="return confirm('آیا از تایید خروج بار اطمینان دارید؟')">ثبت خروج
                        (نگهبانی)
                    </button>

                    <a href="{{route("utility.transport.dashboard.reject_output",[$transport])}}" type="submit"
                       class="btn btn-danger"
                       onclick="return confirm('آیا از عدم تایید خروج بار اطمینان دارید؟')">عدم تایید
                    </a>
                </form>
            @endif


    </div>

</div>

