@php $row=0;@endphp
@if($to_do->showAllLogs($current_user->id))
    @foreach($to_do->logs()->whereNotIn("event_id",[5250002,5250007])->get() as $item)
        <div class="col-sm-12">

            <label class="">
                @switch($item->event_id)
                    @case(5250003)
                    @case(5250004)
                    @case(5250005)
                    شرح
                    @break
                @endswitch

                {{$item->event->caption??$item->event_id}} ({{$item->get_create_date_and_time()}})
                -
                @switch($item->event_id)
                    @case(5250003)
                    توسط {{$item->worker->fullName()}} برای  {{$to_do->worker->fullName()}}
                    :
                    {!! $item->message->text??"ندارد" !!}
                    @break
                    @case(5250004)
                    @case(5250005)
                    توسط {{$to_do->worker->fullName()}}
                    برای  {{isset($item->office_automation_action->worker)?$item->office_automation_action->worker->fullName():"***"}}
                    :
                    {!! $item->message->text??"ندارد" !!}
                    @break
                    @case(5250006)
                    {{$item->worker->fullName()}}
                    @break
                    @default
                    {{$item->worker->fullName()}}
                    :
                    {!! $item->message->text??"" !!}
                @endswitch

            </label>
            @foreach($item->files as $file_item)
                    <a href="{{route("utility.office_automation.dashboard.download",[$item->office_automation_work_id,$file_item])}}">
                        <i class="fa fa-download"></i>
                        {{$file_item->file->caption}}
                    </a>
            @endforeach
        </div>

    @endforeach
@else
    @php $action_log=$to_do->getAction($current_user->id);@endphp

    @if(isset($action_log))

        @foreach($to_do->logs()->whereNotIn("event_id",[5250002])->where(["office_automation_action_id"=>$action_log->id])->get() as $item)
            <div class="col-sm-12">

                <label class="">
                    @switch($item->event_id)
                        @case(5250003)
                        @case(5250004)
                        @case(5250005)
                        شرح
                        @break
                    @endswitch
                    {{$item->event->caption??$item->event_id}} ({{$item->get_create_date_and_time()}}) -

                    @switch($item->event_id)
                        @case(5250003)
                        توسط {{$action_log->worker->fullName()}} برای  {{$to_do->worker->fullName()}}
                        :
                        {!! $item->message->text??"ندارد" !!}
                        @break
                        @case(5250004)
                        @case(5250005)
                        توسط {{$to_do->worker->fullName()}} برای  {{$action_log->worker->fullName()}}
                        :
                        {!! $item->message->text??"ندارد" !!}
                        @break
                        @case(5250006)
                        {{$item->worker->fullName()}}
                        @break
                        @default
                        {{$to_do->worker->fullName()}}
                        :
                        {!! $item->message->text??"" !!}
                    @endswitch

                </label>

                @foreach($item->files as $file_item)
                    <a href="{{route("utility.office_automation.dashboard.download",[$item->office_automation_work_id,$file_item])}}">
                        <i class="fa fa-download"></i>
                        {{$file_item->file->caption}}
                    </a>
                @endforeach
            </div>

        @endforeach

    @endif
@endif

