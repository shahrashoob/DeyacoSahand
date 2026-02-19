<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>
                <a href="{{route("utility.script.1014.edit_machine",$script)}}">
                    لیست ماشین ها
                    ({{isset($data["machines"])?count($data["machines"]):0}})
                </a>
            </h5>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5> لیست پست های سازمان </h5>
        </div>
        <div class="card-block">
            <table class="table table-bordered center">
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
                            {{$item->caption}}

                            ({{$item->worker()->count()}} نفر)
                            <br/>
                            سطح هشدار:
                            <br/>
                            <input type="checkbox"
                                   name="data[post_waning][1][{{$item->id}}]" value="1"
                                {{(isset($data["post_waning"][1][$item->id]) && $data["post_waning"][1][$item->id] ==1)?"checked":"" }}
                            >
                            هشدار سطح 1

                            <input type="checkbox"
                                   name="data[post_waning][2][{{$item->id}}]" value="1"
                                {{(isset($data["post_waning"][2][$item->id]) && $data["post_waning"][2][$item->id] ==1)?"checked":"" }}
                            >
                            هشدار سطح 2

                            <input type="checkbox"
                                   name="data[post_waning][3][{{$item->id}}]" value="1"
                                {{(isset($data["post_waning"][3][$item->id]) && $data["post_waning"][3][$item->id] ==1)?"checked":"" }}
                            >
                            هشدار سطح 3
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
