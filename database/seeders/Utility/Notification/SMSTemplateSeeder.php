<?php

namespace Database\Seeders\Utility\Notification;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SMSTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static $data = [
        'supervisoralertforcreatesuppliercard' => [
            'id' => 1,
            'caption' => 'پیامک ناظر برای ایجاد دستور تامین',
            "text" => 'مدیر محترم %token10دستور تامین مواد اولیه برای %token20 (کد کالا: %token) به مقدار %token2 با سریال تولید %token3 صادر گردید.سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 1
        ],
        'script1027tem1' => [
            'id' => 2,
            'caption' => ' نظارت بر توقف ماشین ها',
            "text" => '%token10 گرامی ماشین %token20 برای مدت %token2 است که در وضعیت %token3 قرار دارد، لطفا به قید فوریت اقدامات لازم را مبذول فرمایید.%tokenسازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'script1019tem1' => [
            'id' => 3,
            'caption' => 'اطلاع رسانی برگشت مواد اولیه',
            "text" => '%token10 گرامی برخی از مواد اولیه ماشین (های) شماره %token2  در %token20 باید برگشت داده شود، لطفا اقدامات لازم را مبذول فرمایید.%tokenسازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'script1017tem1' => [
            'id' => 4,
            'caption' => ' دستیار هوشمند نظارت بر تحویل شیفت',
            "text" => '%token10 گرامیتحویل شیفت ماشین (های) شماره %token2 انجام نشده است، لطفا به قید فوریت اقدامات لازم را مبذول فرمایید.%tokenسازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'script1016tem1' => [
            'id' => 5,
            'caption' => 'دستیار هوشمند ناظر',
            "text" => '%token10 محترم اسکریپت %token به دلیل اجرای ناموفق، متوقف گردیده است.%token20 سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'script1014tem1' => [
            'id' => 6,
            'caption' => 'بررسی خاموشی دستگاه',
            "text" => 'اخطار سطح %token خاموش شدن ماشین%token10 محترمماشین %token20 طی %token2 آینده به علت نداشتن سفارش خاموش خواهد شد.لطفا اقدامات لازم را در این خصوص مبذول فرمایید.%token3سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'script1002tem1' => [
            'id' => 7,
            'caption' => 'گزارش پیامکی کالا براساس وضعیت های مختلف بسته بندی',
            "text" => '%token20مقدار %token %token2، %token10 می باشد.%token3سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'script1013tem1' => [
            'id' => 8,
            'caption' => 'ثبت تراکنش در نرم افزار مالی',
            "text" => '%token20 محترم ثبت تراکنش در %token2 برای %token با شماره پیگیری %token3 ناموفق بوده است، لطفا جهت بررسی به %token10 مراجعه فرمایید.سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'script1006tem1' => [
            'id' => 9,
            'caption' => ' راندمان ایستگاه کاری',
            "text" => '%token20راندمان کل %token از تاریخ %token10, %token2 می باشد. جهت مشاهده جزئیات بر روی لینک زیر کلیک نمایید.https://deyaco.ir/%token3 سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'script1004tem1' => [
            'id' => 10,
            'caption' => ' گزارش میزان تولید استخراج شده',
            "text" => '%token20مقدار کل %token تولید شده از تاریخ %token10, %token2 می باشد. جهت مشاهده جزئیات بر روی لینک زیر کلیک نمایید.https://deyaco.ir/%token3سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'script1007tem1' => [
            'id' => 11,
            'caption' => 'درخواست کالا از طرف انبارک ماشین به انبار',
            "text" => '%token20لطفا نسبت به جمع آوری کالاهای برگ خروج %token10 از انبارک %token و بازگشت آنها به انبار اقدام فرمایید.سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'scriptexecution' => [
            'id' => 12,
            'caption' => 'اجرای  ناموفق دستیار هوشمند',
            "text" => 'همکار گرامیاجرای اسکریپت %token در تاریخ %token2 با شماره مرجع %token3 ناموفق بوده است، ضمن مراجعه به سامانه نسبت به بررسی علت اقدام فرمایید.%token20%token10سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'supervisoralertmachineoff' => [
            'id' => 13,
            'caption' => 'پیامک اخطار خاموش شدن ماشین',
            "text" => 'مدیر محترم %token10ماشین %token20 با کد %token به علت نداشتن سفارش خاموش شد.سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 1
        ],
        'script1006tem2' => [
            'id' => 14,
            'caption' => 'محاسبه راندمان ایستگاه کاری',
            "text" => '%token20راندمان کل %token از تاریخ %token10, %token2 می باشد. سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'supervisoralertforaddpost' => [
            'id' => 15,
            'caption' => 'پیام به ناظر برای افزودن پست',
            "text" => 'مدیر محترم %token20آقا/خانم %token10 به سمت %token  (%token2) منصوب گردید.سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 1
        ],
        'script1004tem2' => [
            'id' => 16,
            'caption' => 'گزارش میزان تولید استخراج شده',
            "text" => '%token20مقدار کل %token تولید شده از تاریخ %token10, %token2 می باشد. جهت مشاهده جزئیات بر روی لینک زیر کلیک نمایید.https://deyaco.ir/%token3سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 2
        ],
        'supervisoralertforcreatecontractorcard' => [
            'id' => 17,
            'caption' => 'پیام به ناظر برای ایجاد دستور پپمان',
            "text" => 'مدیر محترم %token10دستور پیمانی برای %token20 (کد کالا: %token) به مقدار %token2 با سریال تولید %token3 صادر گردید.سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 1
        ],
        'supervisoralertforcreateuser' => [
            'id' => 18,
            'caption' => 'پیام به ناظر برای ایجاد کاربر',
            "text" => 'مدیر محترم %token20کاربری با نام %token10 در %token تعریف گردید.سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 1
        ],
        'supervisoralertforcreateproductioncard' => [
            'id' => 19,
            'caption' => 'پیام به ناظر برای ایجاد کارت تولید',
            "text" => 'مدیر محترم %token10کارت تولیدی برای %token20 (کد کالا: %token) به مقدار %token2 با سریال تولید %token3 صادر گردید.سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 1
        ],
        'supervisoralertforaddcontractor' => [
            'id' => 20,
            'caption' => 'پیام به ناظر برای اضافه کردن پیمانکار',
            "text" => 'مدیر محترم %token20پیمانکاری %token10 با نمایندگی آقا/خانم %token   به عنوان پیمانکار در %token3 تعریف گردید.سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 1
        ],
        'resetpass' => [
            'id' => 21,
            'caption' => 'تغییر کلمه عبور',
            "text" => 'همکار گرامی %token رمز عبور شما با موفقیت تغییر یافت. %token20سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 3
        ],
        'employmentregisterajent' => [
            'id' => 22,
            'caption' => 'ثبت نام نمایندگان',
            "text" => '%token10با توجه به اینکه %token20 شما را به عنوان %token2  خود در %token3 انتخاب کرده است، در صورت تایید با کلیک بر روی لینک زیر فرایند ثبت نام خود را تکمیل نمایید.https://deyaco.ir/%tokenسازمان دیجیتال دیاکو',
            'sms_template_group_id' => 4
        ],
        'employmentcustomercreate' => [
            'id' => 23,
            'caption' => 'رمز و پسورد مشتری',
            "text" => ' %token10
ضمن تقدیر و تشکر از حسن انتخاب شما، نام کاربری شما با مشخصات ذیل تعریف گردید.                     
                    نام کاربری: %token
                    کلمه عبور: %token2
                    با کلیک بر روی لینک زیر می توانید به سامانه وارد شده و سفارشات خود را ثبت نمایید.https://deyaco.ir/%token3%token20
                    سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 4
        ],
        'productcreationprocessnew' => [
            'id' => 24,
            'caption' => 'ثبت طراحی کالا',
            "text" => 'همکار گرامی
                    یک درخواست طراحی کالا با کد %token  از طرف %token10 ثبت گردید.
                    %token20
                    سازمان دیجیتال دیاکو',
            "sms_template_group_id" => 5
        ],
        'employmentinternetaccount' => [
            'id' => 25,
            'caption' => 'دریافت اکانت اینترنت',
            "text" => '%token10
                    اکانت اینترنت شما در %token20 تعریف گردید.
                    نام کاربری: %token
                    کلمه عبور:%token2
                    سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 4
        ],
        'employmentconfirmcontracttype6' => [
            'id' => 26,
            'caption' => 'تایید قرارداد تامین کننده',
            "text" => '%token10
                    اطلاعات تامین شما %token با موفقیت تایید گردید و  در انتظار تایید قرارداد می باشد.
                    لطفا با مراجعه به سامانه نسبت به تایید قرارداد اقدام نمایید.
                    https://deyaco.ir/%token3
                    سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 4
        ],
        'employmentregister6' => [
            'id' => 27,
            'caption' => 'ثبت اولیه همکاری',
            'text' => ' %token10
                        از اینکه %token20 را برای همکاری انتخاب نموده اید سپاس گذاریم. ثبت اطلاعات شما با موفقیت انجام و %token می باشد.
                        اطلاعات بعدی به صورت پیامک برای شما ارسال خواهد شد.
                        https://deyaco.ir/%token3سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 4
        ],
        'employmentconfirmtype1' => [
            'id' => 28,
            'caption' => 'هماهنگی گزنش',
            'text' => '%token10
                        اطلاعات استخدامی شما %token با موفقیت تایید گردید و درخواست شما در انتظار هماهنگی جهت  %token2 می باشد.
                        اطلاعات بعدی به صورت پیامک برای شما ارسال خواهد شد.
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 4
        ],
        'employmentconfirmselection' => [
            'id' => 29,
            'caption' => 'انجام گزینش',
            'text' => '%token10
                        لطفا جهت انجام %token20 در تاریخ %token2 مراجعه فرمایید. 
                        %token
                        برای استفاده از مکان یاب بر روی لینک زیر بزنید.
                        %token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 4
        ],
        'employmentregister' => [
            'id' => 30,
            'caption' => 'ثبت همکاری با ما',
            'text' => ' %token10
                        از اینکه %token20 را برای همکاری انتخاب نموده اید سپاس گذاریم. ثبت اطلاعات شما با موفقیت انجام و %token می باشد.
                        اطلاعات بعدی به صورت پیامک برای شما ارسال خواهد شد.
                        https://deyaco.ir/%token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 4
        ],
        'employmentrejectinfo' => [
            'id' => 31,
            'caption' => 'عدم تایید همکاری با ما',
            'text' => ' %token10
                        اطلاعات %token به علت %token2 تایید نگردید. جهت اصلاح اطلاعات به لینک زیر مراجعه فرمایید.
                        https://deyaco.ir/%token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 4
        ],
        'changepasscustomer' => [
            'id' => 32,
            'caption' => 'تغیر کلمه عبور مشتری',
            'text' => 'مشتری گرامی %token10
                        کلمه عبور شما تغییر یافت، به قید فوریت نسبت به تغییر کلمه عبور خود اقدام نمایید.
                        نام کاربری: %token
                        کلمه عبور: %token2
                        %token20
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 8
        ],
        'changepass' => [
            'id' => 33,
            'caption' => 'تغیر کلمه عبور',
            'text' => 'همکار گرامی %token10
                        کلمه عبور شما تغییر یافت، به قید فوریت نسبت به تغییر کلمه عبور خود اقدام نمایید.
                        نام کاربری: %token
                        کلمه عبور: %token2
                        %token20
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 3
        ],
        'logintoken' => [
            'id' => 34,
            'caption' => 'کد ورود به سامانه',
            'text' => 'کد تایید: %token
                        این کد محرمانه می باشد و تحت هیچ شرایطی آن را در اختیار فرد دیگری قرار ندهید.
                        %token20
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 3
        ],

        'hrleaveconfirmreplaceabsence' => [
            'id' => 35,
            'caption' => 'پیامک جانشینی ',
            'text' => 'همکار گرامی 
                    آقا/خانم %token10 با توجه به اینکه آقا/خانم %token20 غایب هستند و شما می توانید جانشین ایشان باشید، لطفا جهت تایید جانشینی بر روی لینک زیر کلیک نمایید.
                    https://deyaco.ir/%token3
                    %token
                    سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6
        ],
        'hrleaveend' => [
            'id' => 36,
            'caption' => 'پیامک تایید مرخصی و ...',
            'text' => 'همکار گرامی
                        درخواست %token10 شما تایید شد.
                        %token
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6
        ],
        'exeptionerroralert' => [
            'id' => 37,
            'caption' => 'هشدار خطای استثنا',
            'text' => '%token10
                        یک خطای ناگهانی در سامانه رخ داده است:
                        %token20 
                        %token
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 2
        ],
        'allertinfailedautoallocationproductioncardtomachine' => [
            'id' => 38,
            'caption' => 'هشدار در تخصیص ناموفق',
            'text' => '%token10 گرامی
                         دستیار دیجیتال دیاکو در زمان تخصیص %token2 واحد از کارت تولید %token به ماشین به دلیل زیر ناموفق بوده است.
                         %token20
                        %token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 7],
        'machinemoduletypeproperty' => [
            'id' => 39,
            'caption' => 'ویژگی ماژول نوع ماشین',
            'text' => '%token10 گرامی
                        سریال کارت تولید %token، کالای %token2 به دلیل %token20 
                        لطفا اقدامات لازم را مبذول فرمایید.
                        %token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 7],
        'hrreplacementconfirmreplace' => [
            'id' => 40,
            'caption' => 'ییامک تایید جانشینی',
            'text' => 'همکار گرامی 
                    آقا/خانم %token10 با توجه به اینکه آقا/خانم %token20 درخواست جایگزینی به جانشینی شما را دارد، لطفا جهت تایید جانشینی بر روی لینک زیر کلیک نمایید.
                    https://deyaco.ir/%token3
                    %token
                    سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6],
        'machinemoduletype2tem1failuretolaunch' => [
            'id' => 41,
            'caption' => 'عدم راه اندازی شیفت در ماشین ژاکارد',
            'text' => '%token10 گرامی
                        سریال کارت تولید %token، کالای %token2 به دلیل %token20 بر روی ماشین راه اندازی نشد.
                        لطفا اقدامات لازم را مبذول فرمایید.
                        %token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 7],
        'hrleavesetusercommentalert' => [
            'id' => 42,
            'caption' => 'تایید مرخصی و ...',
            'text' => 'همکار گرامی
                        توضیحات %token10، از سوی آقا/خانم %token20 ثبت گردید، لطفا جهت تایید  بر روی لینک زیر کلیک نمایید. 
                        https://deyaco.ir/%token3
                        %token
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6],
        'createcustomer' => [
            'id' => 43,
            'caption' => 'ایجاد مشتری',
            'text' => 'مشتری گرامی %token10
                        نام کاربری شما با مشخصات ذیل تعریف گردید.
                        نام کاربری: %token
                        کلمه عبور: %token2
                        %token20
                        https://deyaco.ir/%token3
                        سازمان دیجتال دیاکو',
            'sms_template_group_id' => 8
        ],
        'rejectproductformforcustomer' => [
            'id' => 44,
            'caption' => ' ثبت مرجوعی کالا برای مشتری',
            'text' => 'مشتری گرامی %token10
                        درخواست مرجوعی شماره %token  از سفارش %token2 توسط واحد کنترل کیفیت تایید گردید.
                        لطفا کالا را به انضمام برگ مرجوعی به کارخانه مرجوع نمایید، جهت دریافت برگ مرجوعی به لینک زیر مراجعه فرمایید.
                        https://deyaco.ir/%token3',
            'sms_template_group_id' => 8
        ],
        'hrleaveconfirmparentpost' => [
            'id' => 45,
            'caption' => 'تایید مرخصی از پست مافوق',
            'text' => 'همکار گرامی
                        یک درخواست %token10، از سوی آقا/خانم %token20 ثبت شده است، لطفا جهت تایید  بر روی لینک زیر کلیک نمایید. 
                        https://deyaco.ir/%token3
                        %token
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6
        ],
        'hrleaveconfirmreplace' => [
            'id' => 46,
            'caption' => 'تایید جانشینی ',
            'text' => 'همکار گرامی 
                    آقا/خانم %token10 با توجه به اینکه آقا/خانم %token20 درخواست %token2 به جانشینی شما را دارد، لطفا جهت تایید جانشینی بر روی لینک زیر کلیک نمایید.
                    https://deyaco.ir/%token3
                    %token
                    
                    سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6],
        'exitformrequirepermission' => [
            'id' => 47,
            'caption' => 'مجوز فرم خروج',
            'text' => '%token10 گرامی %token20
                        برگ خروج شماره %token از سفارش شماره %token2  در انتظار تایید نهایی قرار گرفته است، لطفا جهت تایید آن بر روی لینک زیر کلیک نمایید.
                        https://deyaco.ir/%token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 9
        ],
        'exitformrequiredraftpermission' => [
            'id' => 48,
            'caption' => 'مجوز پیش نویس برک خروج ',
            'text' => '%token10 گرامی %token20
                        برگ خروج شماره %token از سفارش شماره %token2  در انتظار تایید پیش نویس قرار گرفته است، لطفا جهت تایید آن بر روی لینک زیر کلیک نمایید.
                        https://deyaco.ir/%token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 9],
        'createuser' => [
            'id' => 49,
            'caption' => 'ایجاد اکانت همکار',
            'text' => 'همکار گرامی %token10
                        نام کاربری شما با مشخصات ذیل تعریف گردید.
                        نام کاربری: %token
                        کلمه عبور: %token2
                        %token20
                        https://deyaco.ir/%token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 3
        ],
        'ordersmsexistform' => [
            'id' => 50,
            'caption' => 'تاید برگ خروج سفارش',
            'text' => 'مشتری گرامی %token10
                        محموله ای با برگ خروج شماره %token از سفارش شماره %token2 صادر گردید.
                        لطفا پس از دریافت محموله، برای تایید بر روی لینک زیر کلیک نمایید.
                        https://deyaco.ir/%token3
                        با تشکر
                        سازمان دیجتال دیاکو',
            'sms_template_group_id' => 8],
        'ordersms304030' => [
            'id' => 51,
            'caption' => 'تایید پیش فاکتور سفارش',
            'text' => 'مشتری گرامی %token10
                        سفارش شما به شماره %token در انتظار تایید پیش فاکتور می باشد.
                        لطفا جهت تایید پیش فاکتور به  %token20 مراجعه نمایید.
                        https://deyaco.ir/%token3
                        باتشکر
                        سازمان دیجتال دیاکو',
            'sms_template_group_id' => 8],
        'officeautomationworkdone' => [
            'id' => 52,
            'caption' => 'تایید میز کار',
            'text' => '%token10
                        اقدام ارجاع شده شما به شماره %token (%token20) به آقای/خانم %token2 انجام شد، جهت تایید به لینک زیر مراجعه فرمایید.
                        https://deyaco.ir/%token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 10],
        'officeautomationreject' => [
            'id' => 53,
            'caption' => 'ارسال مجدد میزکار',
            'text' => '%token10
                        اقدام ثبت شده شما به شماره %token (%token20) توسط آقای/خانم %token2 ارجاع مجدد شد، جهت بررسی به لینک زیر مراجعه فرمایید.
                        https://deyaco.ir/%token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 10],
        'officeautomationcreatework' => [
            'id' => 54,
            'caption' => 'پیامک به دریافت کننده میز کار',
            'text' => '%token10
                        اقدامی با شماره %token ( %token20) %token2 در میزکار شما قرار گرفت، جهت مشاهده اقدام به لینک زیر مراجعه فرمایید.
                        https://deyaco.ir/%token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 10],
        'officeautomationconfirm' => [
            'id' => 55,
            'caption' => 'تایید میز کار',
            'text' => '%token10
                        اقدام ثبت شده شما به شماره %token (%token20) توسط آقای/خانم %token2 تایید شد، جهت بررسی به لینک زیر مراجعه فرمایید.
                        https://deyaco.ir/%token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 10],
        'allertinremoveallocationproductioncardtomachine' => [
            'id' => 56,
            'caption' => ' حذف کارت تولید از ماشین',
            'text' => '%token10 گرامی
                        کارت تولیدی با سریال %token از ماشین %token20 حذف گردید.
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 7],
        'allertincreateallocationproductioncardtomachine' => [
            'id' => 57,
            'caption' => ' تخصیص کارت تولید به ماشین',
            'text' => '%token10 گرامی
                        کارت تولیدی با سریال %token به ماشین %token20 تخصیص داده شد.
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 7],
        'exitformalarmfortransport' => [
            'id' => 58,
            'caption' => 'هشدار برگ خروج برای حمل و نقل',
            'text' => '%token10 گرامی
                        برگ خروج شماره %token از شماره درخواست %token2  در انتظار ارسال قرار گرفته است، لطفا نسبت به هماهنگی لازم اقدام و جهت ثبت به  %token20 مراجعه نمایید.
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 9
        ],
        'exitformguardingrequirepermission' => [
            'id' => 59,
            'caption' => 'مجوز برگ خروج نگهبانی',
            'text' => '%token10 گرامی %token20
                        باری با برگ خروج شماره %token از سفارش شماره %token2 در انتظار تایید نگهبانی قرار گرفته است، لطفا پس از بررسی برگ خروج را تایید نمایید.
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 9],
        'customerorderchangealert' => [
            'id' => 60,
            'caption' => 'تغییر سفارش مشتری',
            'text' => 'همکار گرامی
                        سفارش %token20 به شماره %token در وضعیت %token2 می باشد، جهت بررسی و تایید سفارش به %token10 مراجعه فرمایید.
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 9],
        'customerresetpass' => [
            'id' => 61,
            'caption' => 'ارسال مجدد رمز عبور مشتری',
            'text' => 'مشتری گرامی %token 
                        رمز عبور شما با موفقیت تغییر یافت. 
                        %token20
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 8
        ],
        'hrleavesetcomment' => [
            'id' => 62,
            'caption' => 'ثتب توضیح برای مرخصی و ...',
            'text' => 'همکار گرامی
                        درخواست %token10، در مرحله ثبت توضیح برای %token20 می باشد، لطفا جهت ثبت توضیحات بر روی لینک زیر کلیک نمایید.
                        http://109.125.144.51:8085/Personal/%token/%token2
                        %token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6
        ],
        'hrentryillegalsignout' => [
            'id' => 63,
            'caption' => ' خروج غیر مجاز',
            'text' => 'همکار گرامی %token10
                        ضمن عرض خسته نباشید، خروج غیر مجاز برای شما ثبت گردید.
                        ساعت و تاریخ خروج: %token - %token2
                        %token20
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6],
        'hrentrysignout' => [
            'id' => 64,
            'caption' => 'خروج همکار',
            'text' => 'همکار گرامی %token10
                        ضمن عرض خسته نباشید، خروج شما با موفقیت ثبت گردید.
                        ساعت و تاریخ خروج: %token - %token2
                        %token20
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6],
        'hrentrysignin' => [
            'id' => 65,
            'caption' => 'ورود همکار',
            'text' => 'همکار گرامی %token10
                        ورود شما با موفقیت ثبت گردید، روز کاری خوشی را برای شما آرزومندیم.
                        ساعت و تاریخ ورود: %token - %token2
                        %token20
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6],
        'addpost' => [
            'id' => 66,
            'caption' => 'افزودن فرد به پست',
            'text' => 'همکار گرامی %token10
                        انتصاب جناب عالی را به عنوان %token (%token2) در %token20 تبریک عرض می نماییم.
                        موفقیت و سربلندی شما را از درگاه خداوند منان مسئلت داریم.
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 3
        ],
        'ordersms3040302' => [
            'id' => 67,
            'caption' => 'تایید پیش فاکتور توسط مشتری',
            'text' => 'مشتری گرامی %token20
                        سفارش شما به شماره %token توسط شما تایید شد و در انتظار بررسی می باشد.
                        %token10
                        سازمان دیجتال دیاکو',
            'sms_template_group_id' => 8],
        'contractorallocation' => [
            'id' => 68,
            'caption' => 'تخصیص دستور پیمان به پیمانکار',
            'text' => 'پیمانکار محترم %token10
                        دستور پیمانی برای %token به مقدار %token2 با سریال %token3 صادر گردید.
                        جهت هماهنگی ارسال مواد اولیه به %token20 مراجعه فرمایید.
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 11],
        'ordersms35060' => [
            'id' => 69,
            'caption' => 'سفارش خاتمه یافته شد',
            'text' => 'مشتری گرامی %token20
                        سفارش شما به شماره %token خاتمه یافته شد.
                        %token10
                        سازمان دیجتال دیاکو',
            'sms_template_group_id' => 8],
        'ordersms35030' => [
            'id' => 70,
            'caption' => 'پردازش سفارش',
            'text' => 'مشتری گرامی %token20
                        سفارش شما به شماره %token پردازش شد و در انتظار آماده سازی می باشد.
                        باتشکر
                        %token10
                        سازمان دیجتال دیاکو',
            'sms_template_group_id' => 8
        ],
        'ordersms304010' => [
            'id' => 71,
            'caption' => 'تایید پیش نویس سفارش',
            'text' => 'مشتری گرامی %token20
                        سفارش شما به شماره %token ثبت گردید و در حال بررسی می باشد.
                        باتشکر
                        %token10
                        سازمان دیجتال دیاکو',
            'sms_template_group_id' => 8
        ],
        'ordersms304010' => [
            'id' => 71,
            'caption' => 'تایید پیش نویس سفارش',
            'text' => '%token10
                    اطلاعات شما به عنوان %token2 در %token تعریف گردید.
                     لطفا جهت تایید قرارداد به لینک زیر مراجعه نموده و قرارداد خود را تایید نمایید.
                    https://deyaco.ir/%token3
                    
                     سامانه  دیجیتال دیاکو ',
            'sms_template_group_id' => 8
        ],

        'hrentrysignoutouto' => [
            'id' => 72,
            'caption' => 'ثبت خروج خودکار',
            'text' => 'همکار گرامی %token10
                با توجه به اینکه حداکثر مدت زمان حضور در سازمان %token3 ساعت می باشد، خروج خودکار برای شما ثبت گردید، خواشمند است در صورت حضور در سازمان نسبت به ورود مجدد اقدام نمایید.
                ساعت و تاریخ خروج: %token - %token2
                %token20
                
                سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 83
        ],


        'speciallicense15tem1' => [
            'id' => 73,
            'caption' => 'تایید مجوز مرجوعی',
            'text' => 'مشتری گرامی %token10
                    مجوز مرجوعی شما برای سفارش %token تایید گردید، شما می توانید  تا تاریخ %token2 کالای مد نظر خود را مرجوع نمایید.
                    توجه داشته باشید که می بایست حتما قبل از مرجوع نمودن کالا نسبت به ثبت ان در سامانه اقدام نمایید.
                    %token20
                    
                    سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 83
        ],
        'employmentconfirmcontract' => [
            'id' => 73,
            'caption' => 'تایید مجوز مرجوعی',
            'text' => 'مشتری گرامی %%token10
                اطلاعات شما به عنوان %token2  در %token تایید گردید،  لطفا جهت تایید قرارداد به لینک زیر مراجعه نموده و قرارداد خود را تایید نمایید.
                https://deyaco.ir/%token3
                
                سازمان دیجیتال دیاکو  ',
            'sms_template_group_id' => 83
        ],

        'employmentcustomerconfirminfo' => [
            'id' => 74,
            'caption' => 'تایید اطلاعات مشتری',
            'text' => 'مشتری گرامی %token10
                اطلاعات شما در  %token20 تایید گردید و در انتظار تنظیم پیش نویس قرار داد هوشمند قرار گرفت.
                اطلاعات بعدی به صورت پیامک برای شما ارسال خواهد شد.
                https://deyaco.ir/%token
                
                سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 83
        ],


        'employmentcustomercontract' => [
            'id' => 75,
            'caption' => 'صدور قرارداد مشتری',
            'text' => '%token10
            قرارداد هوشمند شما در  %token20 صادر گردید و در کارتابل شما قرار گرفته است، لطفا با ورود از طریق لینک زیر نسبت به تایید قرارد اقدام نمایید.
            https://deyaco.ir/%token3
            
            سازمان دیجیتال دیاکو
            ',
            'sms_template_group_id' => 83
        ],

        'exitformrequiredemandspermission' => [
            'id' => 76,
            'caption' => 'مجوز وصول مطالبات برک خروج ',
            'text' => '%token10 گرامی %token20
                        برگ خروج شماره %token از سفارش شماره %token2  در انتظار تایید وصول مطالبات قرار گرفته است، لطفا جهت تایید آن بر روی لینک زیر کلیک نمایید.
                        https://deyaco.ir/%token3
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 9],

        'hrentrysignin2' => [
            'id' => 77,
            'caption' => 'ورود همکار(قالب 2)',
            'text' => 'همکار گرامی %token10
                        زمان ورود: %token - %token2
                        
                        سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6],

        'hrentrysignout2' => [
            'id' => 78,
            'caption' => 'خروج همکار(قالب 2)',
            'text' => 'همکار گرامی %token10
                    زمان خروج: %token - %token2
                    
                    سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6],

        'hrentrynotificationtopost' => [
            'id' => 79,
            'caption' => 'اطلاع رسانی تردد',
            'text' => 'همکار گرامی %ثبت %token %token10
                    زمان:%token2
                    سازمان دیجیتال دیاکو',
            'sms_template_group_id' => 6],

        'productproccesstemplate1' => [
            'id' => 79,
            'caption' => 'اطلاع رسانی طراحی کالا',
            'text' => ' $token1
با توجه به اینکه  درخواست طراحی %token2 در وضعیت %toekn3 قرار گرفته است، جهت اقدام مقتضی به %token10 مراجعه نمایید.
https://deyaco.ir/%token

سازمان دیجیتال دیاکو
لغو11',
            'sms_template_group_id' => 6],


        'specialLicense_temp1' => [
            'id' => 79,
            'caption' => 'اطلاع رسانی تایید مجوز',
            'text' => 'همکار گرامی
                 مجوز  %token10 با کد %token توسط %token3 ثبت گردید و در انتظار  تایید شما می باشد.
                 https://deyaco.ir/%token
                %token20
                سازمان دیجیتال دیاکو
                 ',
            'sms_template_group_id' => 6],


        'changepackingquick' => [
            'id' => 80,
            'caption' => 'اطلاع رسانی تغییر بسته بندی سریع',
            'text' => 'همکار گرامی
                 نوع بسته بندی  %token بسته، از نوع %token2 به %token10 توسط انبار تغییر یافت. 
                 
                %token20
                
                سازمان دیجیتال دیاکو
                 ',
            'sms_template_group_id' => 6],

    ];
    private $table = 'sms_templates';

    public function run()
    {

        foreach (self::$data as $item) {

            if (!DB::table($this->table)->
            where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            } else {
                DB::table($this->table)->where("id", $item["id"])->update($item);
            }

        }
    }
}
