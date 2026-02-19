@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    <p style="text-align: justify">
        لطفاً جهت انجام معاینات قبل از استخدام، فایل مربوطه را دانلود نمایید. سپس با مراجعه به دفتر کارگزینی،
        برگه معرفی نامه را در سربرگ شرکت  و با مهر و امضای مسئولین مربوطه دریافت فرمایید. پس از انجام معاینات طبی،
        لطفاً تمام نتایج را به صورت یک فایل PDF  بارگذاری نمایید.
    </p>
    <p>نشانی مرکز انجام طب کار:
        {{$address_of_work_medicine_doctor_setting}}
    </p>
    <div class="btn btn-outline-primary shadow-2 mb-4">
        <a href="{{ route('hr.employment.register.personal.work_medicine.letter',$employment->key) }}"><i
                    class="fa fa-download"></i> دانلود نامه طب
            کار</a>
    </div>
    <br/>
    <br/>
    @include("component.input._file_upload",["id"=>"work_medicine_file_id",'label'=>"تصویر طب کار", "class_col"=>"",'mark'=>"*"])

    <button class="btn btn-primary shadow-2 mb-4">ثبت</button>
@endsection
