@extends('layouts.admin._master')

@section('page_header_title',"داشبورد انبار  ")
@section('content')

    <div class="row ">
        <div class="col-md-12 ">
          <div class="card">
              <div class="card-header">
                  <h5> تخلیه بار برای {{$form_general_item->product->caption}}</h5>
              </div>
              <div class="card-block">


                  <div class="row">


                      <div class="col-md-12" id="complete_with_valueView">


                          @include("warehouse.input.complete_information_with_value._separation_input_value")</div>


                  </div>


                  <div class="col-md-3"></div>
              </div>
          </div>
        </div>
    </div>


    <script src="{{asset("assets/plugins/jquery/js/jquery.min.js")}}"></script>

    <script>


        function sendToServer(list) {
            {{--var request = fetch('{{url("api/other/data_to_view") }}' ,--}}
            {{--    {--}}
            {{--        method : "POST" ,--}}
            {{--        body : {--}}
            {{--            'data' : 'ali'--}}
            {{--        }--}}
            {{--    }--}}
            {{--);--}}
            var request = $.ajax({
                url: "{{url("api/other/add_form_general_item_form")}}",
                type: "post",
                data: {
                    'data': list
                }
            });
            request.done(function (response, textStatus, jqXHR) {


                $('#complatedWithValueView').html(response);


                //

            });
        }


        let list = [];

        const listElement = $('#list');

        const submitButton = $('#submit');


        let data = [];


        const limit = 5;
        const count = 1;


        $('#count').text(count);
        $('#limit').text(limit);


        function incData(newData) {
            data.push(newData);

        }


        submitButton.click(function (e) {
            e.preventDefault();


            let w = document.forms["myForm"]["weight"];
            let m = document.forms["myForm"]["meter"];
            if (m.value === '' || w.value === '') {

                alert('مقدار به درستی وارد نشده !!');
                return false;
            }


            incData({
                w: w.value,
                m: m.value
            })
            runTask();
            w.value = '';
            m.value = '';


        });


        let average = function () {

            data.forEach(element => {

                let gm = element.w / element.m;
                element['gm'] = Number(gm);
            });
            let countGM = 0;
            let avg = 0;

            for (let index = 0; index < data.length; index++) {

                countGM += data[index].gm;
                avg = countGM / data.length;

            }
            return avg;
        }


        function runTask() {


            let checkerList = [];
            let averageDATA = average();
            for (let i = 0; i < data.length; i++) {


                let e = data[i];
                e['status'] = Math.abs((averageDATA - e.gm)).toFixed(1) < limit ? 'succeed' : 'denied';

                e['status'] === 'succeed' &&
                checkerList.push(e['status']);


                console.log(JSON.stringify(data) + '\n\n' + averageDATA + '\n\n' + checkerList.length);


            }

            sendToServer(JSON.stringify(data));
            if (checkerList.length >= count) {

                // alert(`${count}  مورد تایید شد`);
                $('#uploadBtn').removeClass('invisible');
                // break;
            }


        }

        // runTask();


    </script>
@endsection

@section("styles")
    <style>
        input {
            width: 100px;
        }
    </style>
@endsection



