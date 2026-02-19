@if($employment->employment_selections()->count()>0)

    <div class="col-sm-12">
        <div class="table-responsive center">
            <table class="table table-styling">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th> عنوان</th>
                    <th>وضعیت درخواست همکاری</th>
                    <th>تاریخ هماهنگی</th>
                    <th>امتیاز کسب شده</th>
                    <th> گزینش کنندگان</th>
                </tr>

                </thead>
                <tbody>
                @php $row=0;
                @endphp
                @foreach($employment->employment_selections as $employment_selections_item)
                    <tr>
                        <td>{{++$row}}
                        </td>
                        <td>{{$employment_selections_item->selection->caption}}</td>
                        <td>{{$employment_selections_item->status->caption}}</td>
                        <td>{{$employment_selections_item->get_coordination_time()}}</td>
                        <td>
                            @foreach($employment_selections_item->employment_selection_selectors as $item)
                                @if($item->post->allow_show_result_of_selection == 1 ||  Auth::user()->id == $employment_selections_item->user_id )
                                    <a href="{{route("hr.employment.admin.dashboard.view_selection_result",[$employment,$employment_selections_item->id])}}">{{$employment_selections_item->score_obtained_to_confirm_selection??""}}</a>
                                @endif
                            @endforeach
                        </td>

                        <td>
                            @foreach($employment_selections_item->employment_selection_selectors as $item)
                                <span>  {{$item->post->caption}}، </span>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

@endif
