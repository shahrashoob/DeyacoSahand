<div class="col-md-12">
    <br/>
    <br/>
    <div class="table-responsive">
        <table class="table table-styling">
            <thead>
            <b>لیست گزینش ها
                <a href="{{route("hr.post.post_selection.create",$post)}}">
                    <i class="fa fa-plus"></i> افزودن گزینش جدید
                </a>

            </b>
            <tr>
                <th> #</th>
                <th> عنوان گزینش</th>
                <th>حداقل امتیاز برای تایید گزینش</th>
                <th>اولویت</th>
                <th>آموزش های پیش نیاز</th>
                <th>گزینش کنندگان</th>
                <th></th>

            </tr>

            </thead>
            <tbody>
            @php $row=0;@endphp
            @foreach($selection_setting as $item)
                <tr>
                    <td>{{++$row}}</td>
                    <td>
                        {{$item->selection->caption}}

                    </td>
                    <td>{{$item->minimum_score_to_confirm_selection}}</td>
                    <td>{{$item->priority_number}}</td>

                    <td>
                        <a href="{{route('hr.post.post_selection_education.create',[ $item->post_id,$item->selection_id,$item->id])}}">

                            {{$item->post_selection_education()->count()}}
                            پیش نیاز
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('hr.post.post_selection.add_selector', [$item->selection_id,$item->post_id]) }}">
                            {{$item->selection_selector_posts()->count()}}
                            پست سازمانی
                            و

                            {{$item->selection_selector_committees()->count()}}
                            کمیته
                        </a>
                    </td>
                    <td>
                        <a href="{{route("hr.post.post_selection.destroy",[ $post,$item->id])}}"
                           onclick="return confirm('آیا از حذف تنظیمات مصاحبه اطمینان دارید؟')"><i
                                class="fa fa-trash text-danger"></i> </a>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
