<?php

namespace App\Models\File;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Vmorozov\FileUploads\FilesSaver;
use ErlandMuchasaj\LaravelFileUploader\FileUploader;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        "file_type_id",
        "filename",
        "path",
        "caption",
    ];

    public static function uploadFile($file_request, $file_name, $file_type_id, $path, $save = false)
    {

        $upload_path = Storage::put($path, $file_request);
        $fileName = Arr::last(explode("/", $upload_path));

        if ($save) {
            $file = new File();
            $file->file_type_id = $file_type_id;
            $file->path = $upload_path;
            $file->filename = $fileName;
            $file->caption = $file_request->getClientOriginalName();
            $file->save();

            return $file;
        }
        return ['upload_path' => $upload_path];
    }





    public static function get_file_extension($file_name)
    {
        return substr(strrchr($file_name, '.'), 1);
    }
}
