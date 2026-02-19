<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'مقدار :attribute باید پذیرفته شود.',
    'active_url' => 'آدرس :attribute معتبر نمی‌باشد.',
    'after' => 'تاریخ :attribute باید پس از :date باشد.',
    'after_or_equal' => 'تاریخ :attribute باید برابر یا پس از :date باشد.',
    'alpha' => 'مقدار :attribute فقط می‌تواند حروف باشد.',
    'alpha_dash' => 'مقدار :attribute می‌تواند شامل حروف، اعداد، خط تیره و زیرخط باشد.',
    'alpha_num' => 'مقدار :attribute می‌تواند شامل حروف و اعداد باشد.',
    'array' => 'مقدار :attribute باید یک آرایه باشد.',
    'before' => 'تاریخ :attribute باید قبل از :date باشد.',
    'before_or_equal' => 'تاریخ :attribute باید برابر یا قبل از :date باشد.',
    'between' => [
        'numeric' => 'مقدار :attribute باید بین :min و :max باشد.',
        'file' => 'حجم فایل :attribute باید بین :min و :max کیلوبایت باشد.',
        'string' => 'تعداد کاراکترهای :attribute باید بین :min و :max باشد.',
        'array' => 'تعداد عناصر :attribute باید بین :min و :max باشد.',
    ],
    'boolean' => 'فیلد :attribute باید true یا false باشد.',
    'confirmed' => 'تاییدیه :attribute با مقدار وارد شده مطابقت ندارد.',
    'date' => 'مقدار :attribute یک تاریخ معتبر نمی‌باشد.',
    'date_equals' => 'مقدار :attribute باید برابر با تاریخ :date باشد.',
    'date_format' => 'مقدار :attribute با فرمت :format مطابقت ندارد.',
    'different' => 'مقدار :attribute و :other باید متفاوت باشند.',
    'digits' => 'مقدار :attribute باید :digits رقم باشد.',
    'digits_between' => 'مقدار :attribute باید بین :min و :max رقم باشد.',
    'dimensions' => 'تصویر :attribute ابعاد معتبری ندارد.',
    'distinct' => 'فیلد :attribute تکراری است.',
    'email' => 'مقدار :attribute باید یک ایمیل معتبر باشد.',
    'ends_with' => 'مقدار :attribute باید با یکی از موارد زیر ختم شود: :values.',
    'exists' => ':attribute انتخاب شده معتبر نمی‌باشد.',
    'file' => 'فیلد :attribute باید یک فایل باشد.',
    'filled' => 'فیلد :attribute نباید خالی باشد.',
    'gt' => [
        'numeric' => 'مقدار :attribute باید بزرگتر از :value باشد.',
        'file' => 'حجم فایل :attribute باید بزرگتر از :value کیلوبایت باشد.',
        'string' => 'تعداد کاراکترهای :attribute باید بزرگتر از :value باشد.',
        'array' => 'تعداد عناصر :attribute باید بیشتر از :value باشد.',
    ],
    'gte' => [
        'numeric' => 'مقدار :attribute باید بزرگتر یا برابر با :value باشد.',
        'file' => 'حجم فایل :attribute باید بزرگتر یا برابر با :value کیلوبایت باشد.',
        'string' => 'تعداد کاراکترهای :attribute باید بزرگتر یا برابر با :value باشد.',
        'array' => 'تعداد عناصر :attribute باید :value عدد یا بیشتر باشد.',
    ],
    'image' => 'فایل :attribute باید یک تصویر باشد.',
    'in' => ':attribute انتخاب شده معتبر نمی‌باشد.',
    'in_array' => 'فیلد :attribute در :other موجود نمی‌باشد.',
    'integer' => 'مقدار :attribute باید یک عدد صحیح باشد.',
    'ip' => 'مقدار :attribute باید یک آدرس IP معتبر باشد.',
    'ipv4' => 'مقدار :attribute باید یک آدرس IPv4 معتبر باشد.',
    'ipv6' => 'مقدار :attribute باید یک آدرس IPv6 معتبر باشد.',
    'json' => 'مقدار :attribute باید یک رشته JSON معتبر باشد.',
    'lt' => [
        'numeric' => 'مقدار :attribute باید کمتر از :value باشد.',
        'file' => 'حجم فایل :attribute باید کمتر از :value کیلوبایت باشد.',
        'string' => 'تعداد کاراکترهای :attribute باید کمتر از :value باشد.',
        'array' => 'تعداد عناصر :attribute باید کمتر از :value باشد.',
    ],
    'lte' => [
        'numeric' => 'مقدار :attribute باید کمتر یا برابر با :value باشد.',
        'file' => 'حجم فایل :attribute باید کمتر یا برابر با :value کیلوبایت باشد.',
        'string' => 'تعداد کاراکترهای :attribute باید کمتر یا برابر با :value باشد.',
        'array' => 'تعداد عناصر :attribute نباید بیشتر از :value باشد.',
    ],
    'max' => [
        'numeric' => 'مقدار :attribute نباید بیشتر از :max باشد.',
        'file' => 'حجم فایل :attribute نباید بیشتر از :max کیلوبایت باشد.',
        'string' => 'تعداد کاراکترهای :attribute نباید بیشتر از :max باشد.',
        'array' => 'تعداد عناصر :attribute نباید بیشتر از :max باشد.',
    ],
    'mimes' => 'فایل :attribute باید یکی از انواع :values باشد.',
    'mimetypes' => 'فایل :attribute باید یکی از انواع :values باشد.',
    'min' => [
        'numeric' => 'مقدار :attribute باید حداقل :min باشد.',
        'file' => 'حجم فایل :attribute باید حداقل :min کیلوبایت باشد.',
        'string' => 'تعداد کاراکترهای :attribute باید حداقل :min باشد.',
        'array' => 'تعداد عناصر :attribute باید حداقل :min باشد.',
    ],
    'not_in' => ':attribute انتخاب شده معتبر نمی‌باشد.',
    'not_regex' => 'فرمت :attribute معتبر نمی‌باشد.',
    'numeric' => 'مقدار :attribute باید یک عدد باشد.',
    'password' => 'رمز عبور نادرست است.',
    'present' => 'فیلد :attribute باید وجود داشته باشد.',
    'regex' => 'فرمت :attribute معتبر نمی‌باشد.',
    'required' => 'وارد کردن :attribute الزامی است.',
    'required_if' => 'فیلد :attribute الزامی است زمانی که :other برابر با :value است.',
    'required_unless' => 'فیلد :attribute الزامی است مگر اینکه :other در :values باشد.',
    'required_with' => 'فیلد :attribute الزامی است زمانی که :values وجود دارد.',
    'required_with_all' => 'فیلد :attribute الزامی است زمانی که همه‌ی :values وجود دارند.',
    'required_without' => 'فیلد :attribute الزامی است زمانی که :values وجود ندارد.',
    'required_without_all' => 'فیلد :attribute الزامی است زمانی که هیچ‌کدام از :values وجود ندارند.',
    'same' => ':attribute و :other باید یکسان باشند.',
    'size' => [
        'numeric' => ':attribute باید برابر با :size باشد.',
        'file' => ':attribute باید :size کیلوبایت باشد.',
        'string' => ':attribute باید :size کاراکتر باشد.',
        'array' => ':attribute باید شامل :size آیتم باشد.',
    ],
    'starts_with' => ':attribute باید با یکی از موارد زیر شروع شود: :values.',
    'string' => ':attribute باید یک رشته باشد.',
    'timezone' => ':attribute باید یک منطقه‌ی زمانی معتبر باشد.',
    'unique' => ':attribute قبلاً انتخاب شده است.',
    'uploaded' => 'آپلود :attribute با شکست مواجه شد.',
    'url' => 'فرمت :attribute معتبر نمی‌باشد.',
    'uuid' => ':attribute باید یک UUID معتبر باشد.',
    'education_type_id'=>'نوع آموزش',
    'caption'=>'عنوان',
    'minimumscore_toconfirmtheeducation'=>'حداقل امتیاز برای تایید آموزش',
    'interview_type_id'=>'نوع مصاحبه',
    'email_type' => 'نوع ایمیل',
    'first_name' => 'نام',
    'last_name'=>'نام خانوادگی',
    'user' => 'کاربر',
    'text' => 'متن پیام کوتاه',
    'name' => 'نام',
    'Company_keyword' => 'کلمه کلیدی',
    'company' => 'نام شرکت',
    'phone_number' => 'شماره تلفن',
    'password_confirmation' => 'تایید کلمه عبور',
    'token' => 'توکن',
    'code' => 'کد',
    'persian_name' => 'نام فارسی',
    'procedure' => 'روش پرداخت',
    'gateway' => 'درگاه پرداخت',
    'license_plate'=>'پلاک',
    'car_typeid'=>'نوع خودرو',
    'post_id' => 'پست سازمانی',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'post_id' => 'پست سازمانی',
        'place_of_birth' => 'محل تولد',
        'date_of_birth' => 'تاریخ تولد',
        'date_of_readiness_to_start_work' => 'تاریخ آمادگی جهت شروع به کار',
    ],

];
