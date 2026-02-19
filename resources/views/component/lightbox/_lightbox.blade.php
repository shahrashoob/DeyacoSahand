{{--@include("component.lightbox.lightbox",["src"=>"upload/product/".($product->image->filename??'')])--}}

<div style="width: 90%; height: 90%; text-align: center">
    <img class="example-image" src="{{asset("upload/product/".($product->image->filename??''))}}" alt="" style="width: 90%; height: 90%"/>
</div>