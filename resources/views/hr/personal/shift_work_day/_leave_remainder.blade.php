<div class="row">

    <div class="md-col-12 " style="margin: auto">
        <div class="table-responsive">
            <table class="table table-styling center">
                <thead>
                <tr>
                    <th>سال مالی</th>
                    <th>تاریخ شروع سال مالی</th>
                    <th>تاریخ پایان سال مالی</th>
                    <th>مرخصی ابتدای دوره</th>
{{--                    <th>مرخصی انتهای دوره</th>--}}
                    <th> مانده مرخصی</th>
                </tr>
                </thead>
                <tbody>
                @if($leave_reminder)
                    <tr>
                        <td>{{$leave_reminder->year}}</td>
                        <td>{{$leave_reminder->start_date()}}</td>
                        <td>{{$leave_reminder->end_date()}}</td>
                        <td>{{$leave_reminder->leave_in_start}}</td>
{{--                        <td>{{$leave_reminder->leave_in_end}}</td>--}}
                        <td>{{$leave_reminder->leave_remainder}}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="6">
                            غیر قابل محاسبه
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

    </div>
</div>