<div class="row">
    @include("component.input._text",["id"=>"caption",'label'=>"عنوان ","value"=>$pup_up->caption ??"","autofocus"=>1])

    @include("component.input.datepicker._datepicker",["id"=>"start_date",'label'=>"تاریخ شروع  ","value"=>$pup_up->start_date ??null,"autofocus"=>1])
    @include("component.input..datepicker._datepicker",["id"=>"end_of_date",'label'=>"تاریخ پایان ","value"=>$pup_up->end_of_date ??null])

    @include("component.input._number",["id"=>"number_of_show",'label'=>"تعداد نمایش","value"=>$pup_up->number_of_show ??""])
    @include("component.input._text",["id"=>"version",'label'=>"ورژن نرم افزار","value"=>$pup_up->version ??""])

    <div class="col-md-12">
        <textarea name="message" style="width:700px;height: 200px">
 {{$pup_up->message??""}}
                                     </textarea>

    </div>


    <div class="col-md-12">
        پست های سازمانی:
        <hr style="border: 1px solid; margin-top: 0"/>
    </div>
    @foreach($post_list as $post)
        @if($post->worker->count()>0)
            <div class="col-md-2">
                @include("component.input._checkbox",["id"=>"post_".$post->id,"label"=>$post->caption." (".$post->worker->count().")","checked"=>in_array($post->id,$post_show_ids)])
            </div>
        @endif
    @endforeach

</div>
