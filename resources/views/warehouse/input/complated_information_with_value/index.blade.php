@extends('layouts.admin._master')

@section('page_header_title',"داشبورد انبار  ")
@section('content')

    <div dir="rtl" class="container mt-5 card p-5"
    >

        <h6 class="font-weight-bold  ">تخلیه بار</h6>



{{--        <div   id="list"--}}
{{--               class="d-flex flex-column border-top border-left border-right " >--}}





{{--        </div>--}}

<div class="row">






<div class="col-md-3">

    <span class="row mt-3">تعداد کنترل کیفیت  :   <span id="count"> </span>    </span>
    <span class="row ">     مقدار درستی  :  <span id="limit"></span>  </span>
</div>
        <div class="col-md-6  p-3">

            <form name="myForm" class=" p-1 col-md-6 ">
                @csrf
               <div class="form-group">
                   <label  for="weight">وزن (کیلوگرم)</label>
                   <input   class="form-control" type="number" name="weight" id="w-id"/>
               </div>
              <div class="form-group">
                  <label class="" for="weight">   اندازه (متر) </label>

                  <input class="form-control" type="number" name="meter" id="m-id" />
              </div>
                <button class="btn btn-primary btn-sm hei-30 m-1" type="submit" id="submit">ثبت </button>
            </form>


            <div class="d-flex flex-row  ">
                <span class="  w-25 p-1 ">وزن</span>
                <span class="  w-25 p-1">متر</span>
                <span class="  w-25 p-1">وضعیت</span>



            </div>
            <div  id="complatedWithValueView" >
                    @include("warehouse.input.complated_information_with_value._view")</div>






            <button id="uploadBtn" class="btn btn-success w-100 invisible mt-2">بارگذاری</button>
        </div>


    <div class="col-md-3"></div>
    </div>

    </div>
    <script src="{{asset("assets/plugins/jquery/js/jquery.min.js")}}" ></script>

    <script>


        function  sendToServer(list) {
            {{--var request = fetch('{{url("api/other/data_to_view") }}' ,--}}
            {{--    {--}}
            {{--        method : "POST" ,--}}
            {{--        body : {--}}
            {{--            'data' : 'ali'--}}
            {{--        }--}}
            {{--    }--}}
            {{--);--}}
            var request = $.ajax({
                url: "{{url("api/other/add_data_to_complatedInformationWithValue")}}",
                type: "post",
                data: {
                    'data' : list
                }
            });
            request.done(function (response, textStatus, jqXHR) {


                $('#complatedWithValueView').html(response);


                $('#complatedWithValueView').children(['test']).click(()=>{
                    console.log('hi' + '#complatedWithValueView').children(['test']).value);
                });
                //

            });
        }


        let list = [];

        const listElement = $('#list');

        const submitButton = $('#submit');



        let data = [];


        const limit = 5 ;
        const count = 5 ;


        $('#count').text(count);
        $('#limit').text(limit);



        function incData(newData){
            data.push(newData);

        }



        submitButton.click(function (e){
            e.preventDefault();


            let w = document.forms["myForm"]["weight"];
            let m = document.forms["myForm"]["meter"];
            if(m.value === '' || w.value === ''){

                alert('مقدار به درستی وارد نشده !!');
                return false;
            }


            incData({
                w : w.value ,
                m : m.value
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
            let countGM = 0 ;
            let avg = 0 ;

            for(let index= 0 ;index < data.length; index ++){

                countGM  +=  data[index].gm;
                avg = countGM / data.length;

            }
            return avg;
        }



        function runTask (){


            let checkerList = [];
            let averageDATA = average();
            for(let i = 0 ; i < data.length ; i ++){


                let e = data[i];
                e['status'] =Math.abs((averageDATA  - e.gm)).toFixed(1) < limit   ? 'succeed' : 'denied' ;

                e['status'] === 'succeed' &&
                    checkerList.push(e['status']);




                console.log(JSON.stringify(data)+'\n\n' + averageDATA + '\n\n' + checkerList.length);


            }

            sendToServer(JSON.stringify(data));
            if(checkerList.length >= count){

                // alert(`${count}  مورد تایید شد`);
                $('#uploadBtn').removeClass('invisible');
                // break;
            }


        }

        // runTask();


    </script>
@endsection



