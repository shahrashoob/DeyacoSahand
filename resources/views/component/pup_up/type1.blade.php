@if(isset($pup_up))
    <div class="card-body">
        <div id="exampleModalLong" class="modal fade" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLongTitle" style="display: none;" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{$pup_up->caption??""}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        {!! $pup_up->message??"" !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">بعدا </button>
                        <a href="{{route("confirm_pup_up",$pup_up)}}" class="btn btn-primary">متوجه شدم</a>
                    </div>
                </div>
            </div>
        </div>
        <button id="pup_up" style="display: none" type="button" class="btn btn-primary" data-toggle="modal"
                data-target="#exampleModalLong">پاپ آپ
        </button>

        <script>
            setTimeout(function () {
                $("#pup_up").click()
            }, 1000);
        </script>
    </div>
@endif
