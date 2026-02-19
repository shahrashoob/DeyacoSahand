<!--  -->

<div class="timeline-row">
    <div class="timeline-icon">
        <div class="bg-danger-400">
            <i class="icon-star-full2"></i>
        </div>
    </div>
    <div class="panel panel-flat timeline-content">
        <div class="panel-heading">
            <h5 class="panel-title">
                توضیحات رويداد
            </h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>

        <div class="panel-body">

            <fieldset class="content-group">
                <legend class="text-bold"> {{$event->name}}</legend>
            </fieldset>

            {!! $event->description !!}

                <hr/>
                <div style="text-align: left">
                    <a href="{{url('panel/event/edit/'.$event->id)}}" class="btn btn-primary"><i class="icon icon-pencil"></i> ویرایش توضیحات </a>
{{--                    <a href="{{url('panel/event/delete/'.$event->id)}}" class="btn btn-danger"> <i class="icon icon-trash"> حذف </i> </a>--}}
                </div>

        </div>
    </div>

</div>
<!-- / -->
