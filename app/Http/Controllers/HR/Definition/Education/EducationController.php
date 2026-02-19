<?php

namespace App\Http\Controllers\HR\Definition\Education;

use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\HR\Education\Education;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;



class EducationController extends Controller
{
    private $view_path = "hr.definition.education.education.";
    private $route_path = "hr.definition.education.education.";


    //نمایش صفحه افزودن اموزش
    public function create()
    {
        $educations_option = Option::get("education_type");
        $educations_exam_option = Option::get("exam_type");
        return view($this->view_path . "create", compact('educations_option','educations_exam_option'));
    }


    //ثبت آموزش جدید
    public function store(Request $request)
    {

        $request->validate([
            'education_type_id' => ['required'],
            "caption" => ['required'],
            'minimum_score_to_confirm_the_education' => ['required'],
        ]);
        $text_file = null;
        if ($request->hasFile('educational_text_file_id')) {
            $text_file = File::uploadFile($request->file('educational_text_file_id'), 'text_file.' . File::get_file_extension($request->file('educational_text_file_id')->getClientOriginalName()), 60, 'upload/education/', true);
        }
        $video_file = null;
        if ($request->hasFile('educational_video_file_id')) {
            $video_file = File::uploadFile($request->file('educational_video_file_id'), 'video_file.' . File::get_file_extension($request->file('educational_video_file_id')->getClientOriginalName()), 70, 'upload/education/', true);
        }
        $education = Education::create([
            'education_type_id' => $request->education_type_id,
            'exam_type_id' => $request->exam_type_id,
            "educational_text_file_id" => $text_file ? $text_file->id : null,
            "educational_video_file_id" => $video_file ? $video_file->id : null,
            "caption" => $request->caption,
            "minimum_score_to_confirm_the_education" => $request->minimum_score_to_confirm_the_education,

        ]);

        return redirect()->route($this->route_path . "index")->with(["success" => "یک آموزش با موفقیت اضافه شد"]);
    }


    //نمایش صفحه لیست آموزش
    public function index()
    {
        $educations = Education::all();
        return view($this->view_path . "index", compact('educations'));
    }


    //حذف آموزش دلخواه
    public function destroy($id)
    {

        $education = Education::find($id);
        $education->delete();
        return redirect()->route($this->route_path . "index")->with(["success" => "یک آموزش با موفقیت حذف گردید"]);

    }

    // نمایش ادیت آموزش
    public function edit(Education $education)
    {

        $education_option = Option::get("education_type", $education->education_type_id);
        $education_exam_option = Option::get("exam_type", $education->exam_type_id);
        return view($this->view_path . "edit", compact("education_option", 'education','education_exam_option'));

    }

//    ادیت آموزش
    public function update(Request $request, Education $education)
    {

        $education->caption = $request->input('caption');
        $education->education_type_id= $request->input('education_type_id');
        $education->minimum_score_to_confirm_the_education=$request->input('minimum_score_to_confirm_the_education');
        $education->exam_type_id= $request->input('exam_type_id');

        // بررسید که آیا فایل متنی آموزشی جدیدی آپلود شده است یا خیر
        if ($request->hasFile('educational_text_file_id')) {
            $text_file = File::uploadFile(
                $request->file('educational_text_file_id'),
                "text_file_".$education->id."." . File::get_file_extension($request->file('educational_text_file_id')->getClientOriginalName()),
                60,
                'upload/education/',
                true
            );
            $education->educational_text_file_id = $text_file->id;
        }

        // بررسی که آیا فایل ویدیویی آموزشی جدیدی آپلود شده است یا خیر
        if ($request->hasFile('educational_video_file_id')) {
            $video_file = File::uploadFile(
                $request->file('educational_video_file_id'),
                'video_file.' . File::get_file_extension($request->file('educational_video_file_id')->getClientOriginalName()),
                70,
                'upload/education/',
                true
            );
            $education->educational_video_file_id = $video_file->id;
        }

        $education->save();

    return redirect()->route( $this->route_path . "index" )->with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }
//    public function update(Request $request, Education $education)
//    {
//        $education->caption = $request->input('caption');
//        $education->education_type_id = $request->input('education_type_id');
//        $education->minimum_score_to_confirm_the_education = $request->input('minimum_score_to_confirm_the_education');
//        $education->exam_type_id = $request->input('exam_type_id');
//
//        $text_file = $education->educational_text_file_id; // حافظه‌ای برای فایل قبلی
//        if ($request->hasFile('educational_text_file_id')) {
//            // اگر فایل جدید آپلود شده است، فایل قبلی حفظ نمی‌شود
//            $text_file = File::uploadFile($request->file('educational_text_file_id'), "text_file_" . $education->id . "." . File::get_file_extension($request->file('educational_text_file_id')->getClientOriginalName()), 60, 'upload/education/', true);
//        }
//
//        $video_file = $education->educational_video_file_id; // حافظه‌ای برای فایل قبلی
//        if ($request->hasFile('educational_video_file_id')) {
//            // اگر فایل جدید آپلود شده است، فایل قبلی حفظ نمی‌شود
//            $video_file = File::uploadFile($request->file('educational_video_file_id'), 'video_file.' . File::get_file_extension($request->file('educational_video_file_id')->getClientOriginalName()), 70, 'upload/education/', true);
//        }
//
//        $education->educational_text_file_id = $text_file ? $text_file->id : null;
//        $education->educational_video_file_id = $video_file ? $video_file->id : null;
//
//        $education->save();
//
//        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);
//    }

    //تابع دانلود
    public function download( Education $education, File $file)
    {

        $path     = public_path($file->path );
        $fileName = $file->caption;
        return Response::download( $path, $fileName, [ 'Content-Type: application' ] );

    }
}
