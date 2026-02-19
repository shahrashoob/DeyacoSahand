<form id="form_api">
    <div class="card">
        <div class="card-header">
            <h5>ثبت بسته بندی  :   {{isset($product)?$product->fullCaption():""}} </h5>

            <div class="row">
                <div class="col-md-3"></div>
                <div class=" col-md-6 col-sm-12 center">
                    <div class="row">
                        <div class="alert alert-danger" id="alert_danger_select" style="display: none;margin: auto"></div>
                        @if(isset($error_message))

                            <div class="alert alert-danger col-md-12">
                                {!! $error_message !!}
                            </div>
                            <script>
                                var snd = new Audio("{{asset("assets/voice/alarm.mp3")}}");
                                snd.play();
                            </script>

                        @endif
                        @if(isset($success_message))

                            <div class="alert alert-success col-md-12">
                                {!! $success_message !!}
                            </div>
                                <script>
                                    var snd = new Audio("{{asset("assets/voice/beep.mp3")}}");
                                    snd.play();
                                </script>
                        @endif
                        <div class="col-md-12 col-sm-12 " style="margin-top: 10px">
                            <input type="radio" id="type_of_reading_1" name="type_of_reading" value="packing_form_code" checked>
                            <label for="type_of_reading_1">
                                {{$allow_entry_with_pin?"بر اساس کد پین":" بر اساس کد بسته بندی"}}
                            </label>
                            <input type="radio" id="type_of_reading_2" name="type_of_reading" value="transport_code"> <label for="type_of_reading_2">بر اساس کد بسته بندی حمل و نقل</label>
                        </div>
                        <div class="col-md-12 col-sm-12 " style="margin-top: 10px">

                            <input id="packing_code" type="number" style="width: 120px; height: 35px"
                                   value="">
                            <div class="d-block d-sm-none" style="margin: 10px" >



                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" style="margin: 0px"
                                    id="submit_packing_code">بررسی و ثبت
                            </button>
                        </div>





                    </div>


                </div>

                <div class="col-md-6 center" style="margin:auto">

                        @if(isset($packing_message) && $number_packing_submit>0)

                                    <div class="alert alert-info ">
                                        لیست بسته بندی های ثبت شده:
                                        <br/>
                                        @foreach(explode(',',$packing_message) as $row)
                                            {{$row}} <br/>
                                        @endforeach

                                    </div>

                        @endif

                </div>
            </div>
        </div>
    </div>
</form>



