<?php

namespace App\Models\HR\Employment;

use App\Http\Controllers\HR\Employment\Register\PersonalInfoController;
use App\Models\File\File;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Post\PostDocumentType;
use App\Models\Utility\Address\Country;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Document\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmploymentDocumentType extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'employment_id',
        'file_id',
        'document_type_id',
        'receive_document_step_id',
        'other_id',
    ];
    protected $table = 'employment_document_types';

    public function document_type()
    {
        return $this->belongsTo(DocumentType::class,);
    }

    public function file()
    {
        return $this->belongsTo(File::class,);
    }

    public static function CreateEmploymentDocumentType($employment, $request, $step_id, $other_id)
    {
// این تابع برای اپلود همه مدارک همکاری با ما نوشته شده است.
        if ($employment->status_id == 4640107) {
            $confirm_type = 2;
        } elseif ($employment->status_id == 4640301) {
            $confirm_type = 1;
        } else {
            $confirm_type = 3;
        }

        //تابع بارگزاری مدارک استخدام
        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::join('post_document_receive_step_confirms',
            'document_receive_step_document_types.receive_document_step_id', 'post_document_receive_step_confirms.receive_document_step_id')->
        where([
            "post_document_receive_step_confirms.post_id" => $employment->post_id,
            "post_document_receive_step_confirms.confirm_type" => $confirm_type,
            "document_receive_step_document_types.receive_document_step_id" => $step_id,
        ])->
        select('document_receive_step_document_types.id', 'document_receive_step_document_types.document_type_id', 'post_document_receive_step_confirms.confirm_type',
            "document_receive_step_document_types.receive_document_step_id", 'post_document_receive_step_confirms.is_necessary_to_deliver_document_to_archive')->
        get();

        if (!$document_receive_step_document_type_list) {
            return [
                "result" => false,
                "error" => "بارگذاری انجام نشد، لطفا با پشتیبانی تماس بگیرید."
            ];
        }
        if ($other_id) {
            $old_employment_document_type = EmploymentDocumentType::where([
                'employment_id' => $employment->id,
                'other_id' => $other_id,
                'receive_document_step_id' => $step_id,
            ])->get()->keyBy('document_type_id');
        } else {
            $old_employment_document_type = EmploymentDocumentType::where('employment_id', $employment->id)->get()->keyBy('document_type_id');
        }


        $errors = [];

        foreach ($document_receive_step_document_type_list as $item) {
            //بارگزاری فایل مدارک استخدام
            $file = null;
            $input_file = 'file_' . $item->document_type_id;

//                if ($employment->worker->gender_id == 1 && $item->document_type_id == 10) {
//                    continue;
//                }

            if ($item->document_type->nationality_id == $employment->nationality_id || is_null($item->document_type->nationality_id)) {
                if (!$request->hasFile($input_file) && empty($old_employment_document_type->get($item->document_type_id))) {
                    $errors[] = "لطفاً " . $item->document_type->caption . " را بارگزاری کنید.";
                }
            }

            $result_file = PersonalInfoController::checkFileUploded($request, $input_file, ["png", 'jpg', 'jpeg', 'pdf', 'xls', 'xlsx', 'zip', 'doc', 'docx']);
            if (!$result_file["result"]) {
                $errors[] = $result_file["error"] . "(" . $item->document_type->caption . ")";
            }
        }
        if (!empty($errors)) {
            $response = [
                "result" => false,
                "error" => $errors,
            ];

        } else {
            //اگز خطایی نداشت ذخیره کند
            foreach ($document_receive_step_document_type_list as $item) {

                //بارگزاری فایل مدارک استخدام
                $file = null;
                $input_file = 'file_' . $item->document_type_id;
                if ($request->hasFile($input_file)) {
                    $file = File::uploadFile(
                        $request->file($input_file),
                        $employment->id . $item->document_type_id . Str::random(15) . File::get_file_extension($request->file($input_file)->getClientOriginalName()),
                        90,
                        'upload/employment_document_type', true);


                    if (empty($old_employment_document_type->get($item->document_type_id))) {
                        EmploymentDocumentType::create([
                            'user_id' => $employment->user_id,
                            'employment_id' => $employment->id,
                            'file_id' => $file ? $file->id : null,
                            'document_type_id' => $item->document_type_id,
                            'receive_document_step_id' => $item->receive_document_step_id,
                            'other_id' => isset($other_id) ? $other_id : null,
                        ]);

                    } else {

                        $old_file_path = $old_employment_document_type->get($item->document_type_id)->file->path;
                        unlink($old_file_path);
                        $old_employment_document_type->get($item->document_type_id)->file->delete();
                        $old_employment_document_type->get($item->document_type_id)->file_id = $file ? $file->id : null;
                        $old_employment_document_type->get($item->document_type_id)->save();
                    }

                }
            }
            $response = [
                "result" => true,
                "message" => "اطلاعات شما با موفقیت ثبت گردید.",
                "count" => $document_receive_step_document_type_list->count()

            ];
        }
        return $response;
    }


}
