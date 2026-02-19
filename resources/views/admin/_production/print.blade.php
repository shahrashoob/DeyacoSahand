<html>
<head>
    @include("pdf._head")
</head>
<body>
    
<div class="content">
   
    <table>
            <tr>
                <td>تاریخ</td>
                <td>ساعت شروع</td>
                <td>ساعت پایان</td>
                <td>زمان شیفت </td>
                <td>شاخص خرد</td>
                <td>افراد شاغل</td>
            </tr>
            @foreach($production->datetimes as $datetime)

            <tr>
                <td>
                    {{jdate( \Carbon\Carbon::parse($datetime->start_datetime)
                    ->timestamp)->format('Y/n/j')}}
                </td>
               <td>
                {{jdate( \Carbon\Carbon::parse($datetime->start_datetime)
                    ->timestamp)->format('H:i:s')}}
               </td>
               <td>
                {{jdate( \Carbon\Carbon::parse($datetime->end_datetime)
                    ->timestamp)->format('H:i:s')}}  
               </td>
               <td>
                   {{$datetime->shift_time}} ساعت
               </td>
               <td>
                   {{$datetime->sub_productivity_index}}
               </td>
                <td>
                   @foreach($datetime->production_worker as $pworker )
                   {{$pworker->worker->fullname()." (".$pworker->post_name.")"}}
                   <br/>
                   @endforeach
                </td>
            </tr>
                        
            @endforeach
    </table>

<br/><br/>


    <table style=" display:inline">
        <tr>
            <td>تاریخ</td>
            <td>{{jdate( \Carbon\Carbon::parse($production->date_of_production_date)
                ->timestamp)->format('Y/n/j')}}</td>

            <td>
                تاریخ و ساعت سفارش گذاری

            </td>
        </tr>
        <tr>
            <td>شماره سریال تولید</td>
            <td>{{$production->production_series}}</td>

            <td>
            {{$production->order_date}}
            </td>
           
           
        </tr><tr>
            <td>زمان مجاز بیکاری (دقیقه)</td>
            
            <td>{{$production->unemployment_time}}</td>
            <td>
                تاریخ و زمان تحویل به واحد تولید  

        </td>
            

        </tr><tr>
            <td>دون تایم خط (دقیقه)</td>
            <td>{{$production->down_time}}</td>
            <td>
                {{jdate( \Carbon\Carbon::parse($production->order_time)
                    ->timestamp)->format('H:i:s - Y/n/j ')}}
            </td>

           

        </tr><tr>
            <td>زمان ست آپ (دقیقه)</td>
            <td>{{$production->set_up_time}}</td>
            <td>تاریخ و ساعت تحویل محصول به قرنطینه</td>
        </tr><tr>
            <td>سرپرست تولید</td>
            <td> {{$production->supervisor_worker->firstname." ".$production->supervisor_worker->lastname}}</td>
            <td></td>

        </tr><tr>
            <td>نام محصول</td>
            <td>{{$production->product_name}}</td>
            <td>
                تاریخ و ساعت تایید کنترل کیفیت
        </td>
        </tr><tr>
            <td>کد محصول</td>
            <td>{{$production->product_code}}</td>
            
            <td></td>
        </tr><tr>
            <td>نام خط</td>
            <td>{{$production->line->name}}</td>
            
            <td>
                تاریخ و ساعت تحویل به انبار محصول
            </td>
            </tr><tr>
            <td>کد خط</td>
            <td>{{$production->line->code}}</td>
            <td></td>
        </tr>
        <tr>
            <td>تعداد تولید شده</td>
            <td>{{$production->number_product}}</td>
           
            <td>تاریخ وساعت تحویل کارت به واحد برنامه ریزی تولید</td>
        </tr>
        <tr>
            <td>تعداد تکی</td>
            <td>{{$production->sub_number_product}}</td>
            <td></td>
        </tr>
        
        <tr>
            <td> شاخص ارزیابی عملکرد</td>
            <td>{{$production->productivity_index}}</td>
            
        </tr>
        
    </table>
    
{{-- 
<table style=" display:inline">
  
        <tr>
           
        </tr>

        <tr>
           
        </tr>
        

        <tr>
            
        </tr>
        
        <tr>
           
        </tr>
        
        
        <tr>
            <td>
                    تاریخ و زمان تحویل به  واحد   

            </td>
        </tr>
        
        
        <tr>
            <td>
                    تاریخ و زمان ثبت فرم  

            </td>
        </tr>
   </table>




</div> --}}


</div>
</body>
</html>
