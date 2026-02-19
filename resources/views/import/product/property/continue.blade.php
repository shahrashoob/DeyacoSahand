@extends('layouts.admin._master')


@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">

                        <div style="text-align: center" class="col-md-12">
                            <h5>تعداد رکورد باقی مانده: {{$count}}</h5>
                            <br/>
                            <br/>
                            <b>در صورتی که بعد از یک ثانیه، صفحه رفرش نشده، بر روی دکمه زیر کلیک کنید.
                            </b> <br/>
                            <br/> <br/>
                            <a href="{{route("import.product.property.update")}}" class="btn btn-primary">
                                ادامه اعمال تغییرات ...</a>

                            </a>


                        </div>
                    </div>
                    <br/>
                    <br/>


                </div>

            </div>
        </div>
    </div>
@endsection

@section("styles")

    @include("component.smartwizard.script")
    <script>
        setTimeout(function () {
            window.location.href = "{{route('import.product.property.update')}}";
        }, 1500)
    </script>
@endsection
