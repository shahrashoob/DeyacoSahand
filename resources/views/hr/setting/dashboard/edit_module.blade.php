@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">
                    <h5>ویرایش ماژول {{$shift_delivery_module->caption}}</h5>

                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.setting.dashboard.update_module",$shift_delivery_module)}}"
                          method="post" novalidate="novalidate">
                        @csrf

                        <b>آیا حضور فرد در سازمان چک شود؟</b>:
                        <input
                            name="presence_in_the_organization_checked"
                            type="radio"
                            {{$shift_delivery_module->presence_in_the_organization_checked==1?"checked":""}}
                            value="1"
                        /> بله

                        <input
                            name="presence_in_the_organization_checked"
                            type="radio"
                            {{$shift_delivery_module->presence_in_the_organization_checked==0?"checked":""}}
                            value="0"
                        />خیر
                        <br/>
                        <br/>

                        <b>افرادی که تحویل شیفت دارند،<br/> قبل از تایید تحویل شیفت می توانند از سازمان خارج شوند؟</b>:
                        <input
                            name="people_who_have_a_delivery_shift_have_permission_to_exit"
                            type="radio"
                            {{$shift_delivery_module->people_who_have_a_delivery_shift_have_permission_to_exit==1?"checked":""}}
                            value="1"
                        /> بله

                        <input
                            name="people_who_have_a_delivery_shift_have_permission_to_exit"
                            type="radio"
                            {{$shift_delivery_module->people_who_have_a_delivery_shift_have_permission_to_exit==0?"checked":""}}
                            value="0"
                        />خیر
                        <br/>
                        <br/>
                        <b>آیا افرادی که تحویل شیفت دارند، پس از تحویل شیفت، می توانند از سازمان خارج شوند؟</b>:
                        <input
                            name="people_in_the_next_delivery_have_permission_to_exit"
                            type="radio"
                            {{$shift_delivery_module->people_in_the_next_delivery_have_permission_to_exit==1?"checked":""}}
                            value="1"
                        /> بله

                        <input
                            name="people_in_the_next_delivery_have_permission_to_exit"
                            type="radio"
                            {{$shift_delivery_module->people_in_the_next_delivery_have_permission_to_exit==0?"checked":""}}
                            value="0"
                        />خیر
                        <br/>
                        <br/>
                        <br/>
                        <div class="col-md-12">
                            <a class="btn btn-outline-dark" href="{{route("hr.setting.dashboard.index")}}">بارگشت</a>
                            <button type="submit" class="btn btn-primary">
                                ذخیره تغییرات
                            </button>
                        </div>

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
