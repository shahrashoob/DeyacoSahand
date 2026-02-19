<div class="col-md-12">
<div class="card">
    <div class="card-header">
        <h5> لیست پست های سازمان </h5>
    </div>
    <div class="card-block">
        <table class="table table-bordered center" >
            @php $row=0;@endphp
            <tr>
                <th>عنوان پست</th>
                <th>عنوان پست</th>
                <th>عنوان پست</th>
            </tr>
            <tr>
                @foreach($posts as $item)
                    @php $key="k".$item->id;@endphp
<td>
                        <a href="{{route("utility.script.1006.edit_post",[$script,$item])}}">{{$item->caption}}</a>

                        ({{$item->worker()->count()}} نفر)
                        <br/>
                        :قالب پیامک
                        <br/>
                        <input type="radio"
                               name="data[post_sms_template][{{$item->id}}]" value="1"
                            {{(isset($data["post_sms_template"][$item->id]) && $data["post_sms_template"][$item->id] ==1)?"checked":"" }}
                        >
                        با لینک داشبورد

                        <input type="radio"
                               name="data[post_sms_template][{{$item->id}}]" value="2"
                            {{(isset($data["post_sms_template"][$item->id]) && $data["post_sms_template"][$item->id]==2)?"checked":"" }}
                        >
    بدون لینک داشبورد

                        <input type="radio"
                               name="data[post_sms_template][{{$item->id}}]" value="0"
                            {{(isset($data["post_sms_template"][$item->id]) && $data["post_sms_template"][$item->id]==0)?"checked":"" }}
                        >
                       عدم ارسال
                    </td>
                    @if($row % 3==2)
            </tr>
            <tr>
                @endif
                @php $row++;@endphp
                @endforeach
            </tr>
        </table>

        <div class="float-left">
            نمايش رکوردهای
            <b>{{$posts->firstItem()}}</b>
            تا
            <b>{{$posts->lastItem()}}</b>
            از
            <b>{{$posts->total()}}</b>
            رکورد موجود


        </div>
    </div>
    <div class="text-center">
        {{$posts->links('pagination::bootstrap-4')}}
    </div>
</div>
</div>
