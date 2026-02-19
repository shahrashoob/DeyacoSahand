@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5> ویژگی های {{$contractor->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form2"
                          action="{{route("contractor.definition.property.update",$contractor)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @foreach($list as $item)

                                @if($item->field_type_id!=3)
                                    @include("component.input._text",["id"=>"p_".$item->id,'label'=>$item->caption,"value"=>$contractor->get_property_value($item->id)])
                                @else
                                    @include("component.input._select",["id"=>"p_".$item->id,'label'=>$item->caption,"option"=>$option[$item->id]["items"]])
                                @endif


                            @endforeach

                        </div>
                        <a href="{{route("contractor.definition.dashboard.index",$contractor)}}"
                           class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form2').validate({
            rules: {
                @foreach($list  as $item)
                @if($item->field_type_id!=3)
                @php echo 'p_'.$item->id.':{required:true,min:'.$item->min_value.",max:".$item->max_value.' },';@endphp
                @else
                @php echo 'p_'.$item->id.':{required:true },';@endphp

                @endif

                @endforeach
            }
        });
    </script>
@endsection
