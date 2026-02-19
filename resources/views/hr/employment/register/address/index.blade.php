@extends('hr.employment.register.layout._layout',["title_caption"=>"تکمیل اطلاعات تماس و آدرس"])

@section('content')

    <div class="col-md-12 content-class">

        @if( $employment->status_address_id!=4641402)
            <form id="form1" method="post"
                  action="{{route('hr.employment.register.address.submit',$employment->key)}}"
                  enctype="multipart/form-data" autocomplete="false">
                @csrf

                @php $panel_name="address" @endphp


                @include("hr.employment.register.address._address_info")


                <div class="center">
                    <br/>
                    <a class="btn  mb-4"
                       href="{{route("hr.employment.register.personal_info.index",$employment->key)}}">بازگشت</a>
                    <button class="btn btn-primary shadow-2 mb-4">ثبت و ادامه</button>
                    <br/>
                </div>


            </form>
        @else
            @php $panel_name="address" @endphp
            @include("hr.employment.register.address._show")
            <div class="center">
                <br/>
                <a class="btn  mb-4" href="{{route("hr.employment.register.personal_info.index",$employment->key)}}">بازگشت</a>
                @if( in_array($employment->cooperation_type_id,[2,3,6] )&& $employment->personal_type_id==1  )
                    <a class="btn btn-primary shadow-2 mb-4"
                       href="{{route("hr.employment.register.other.index",$employment->key)}}">بعدی</a>
                @endif
                @if($employment->cooperation_type_id== 3 && $employment->personal_type_id==2)
                    <a class="btn btn-primary shadow-2 mb-4"
                       href="{{route("hr.employment.register.customer.agent.index",$employment->key)}}">بعدی</a>
                @endif
                @if($employment->cooperation_type_id== 6 && $employment->personal_type_id==2)
                    <a class="btn btn-primary shadow-2 mb-4"
                       href="{{route("hr.employment.register.supplier.agent.index",$employment->key)}}">بعدی</a>
                @endif
                @if($employment->cooperation_type_id== 2 && $employment->personal_type_id==2)
                    <a class="btn btn-primary shadow-2 mb-4"
                       href="{{route("hr.employment.register.contractor.agent.index",$employment->key)}}">بعدی</a>
                @endif
                @if(in_array($employment->cooperation_type_id,[1,11]) && $employment->personal_type_id==1)
                <a class="btn btn-primary shadow-2 mb-4"
                   href="{{route("hr.employment.register.personal.academic_degree.index",$employment->key)}}">بعدی</a>
                <br/>
                @endif
            </div>

        @endif
    </div>

@endsection

@section("scripts")

    <script type="text/javascript">
        $('#form1').validate({
            rules: {
                country_id: "required",
                province_id: "required",
                city_name: "required",
                address: "required",
                state_id: "required",
                city_id: "required",
                country_id: "required",
                mobile: {
                    required: true,
                    number: true,
                    minlength: 10, maxlength: 10
                },

                phone: {
                    required: true,
                    number: true,
                    minlength: 11, maxlength: 11
                },

                postal_code: {
                    required: true,
                    number: true,
                    minlength: 10, maxlength: 10
                },
            }
        });

    </script>

@endsection
