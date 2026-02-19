

<div class="md-modal md-effect-{{$id}}" id="modal-{{$id}}">
    <form id="form-{{$id}}" method="post" action="{{url($url)}}"  enctype="multipart/form-data">
        @csrf
        <div class="md-content">
            <h3 class="theme-bg{{$theme}}">{{$title}}</h3>
            <div>

                {!! $content !!}

            </div>
        </div>
    </form>
</div>

