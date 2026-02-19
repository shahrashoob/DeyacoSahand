<div style="display: inline-block; ">
    <form action="{{route($route)}}" method="post">
        @csrf
        @include("production.details_dashboard._hidden_inputs")
        @include("component.input._hidden",["value"=>1,"id"=>"export_excel"])

        <button type="submit" style="background: none; border: none" class=" text-primary dropdown-toggle ">
            فایل اکسل
            <i class="fa fa-file-excel"></i>
        </button>
        @if($large_operation)
            <a href="{{route("production.dashboard.download_excel",$large_operation)}}" style="display: inline-block">
                <i class="fa fa-download"></i>

                دانلود فایل اکسل گزارش -
                {{$large_operation->get_created_at()}}
            </a>
        @endif
    </form>


</div>