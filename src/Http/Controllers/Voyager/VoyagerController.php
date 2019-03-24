<?php

namespace Modules\Admin\Http\Controllers\Voyager;

use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerController as BaseVoyagerController;

class VoyagerController extends BaseVoyagerController
{
    public function index()
    {
        return Voyager::view('admin::voyager.index');
    }

    public function profile()
    {
        return Voyager::view('admin::voyager.profile');
    }

    public function upload(Request $request)
    {
        $fullFilename = null;
        $resizeWidth = 1800;
        $resizeHeight = null;
        $slug = $request->input('type_slug');
        $file = $request->file('image');

        $path = $slug.'/'.date('F').date('Y').'/';

        $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
        $filename_counter = 1;

        // Make sure the filename does not exist, if it does make sure to add a number to the end 1, 2, 3, etc...
        while (Storage::disk(config('voyager.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
            $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension()).(string) ($filename_counter++);
        }

        $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

        $ext = $file->guessClientExtension();

        if (in_array($ext, ['jpeg', 'jpg', 'png', 'gif'])) {
            $image = Image::make($file)
                ->resize($resizeWidth, $resizeHeight, function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($file->getClientOriginalExtension(), 75);

            // move uploaded file from temp to uploads directory
            if (Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image, 'public')) {
                $status = __('voyager::media.success_uploading');
                $fullFilename = $fullPath;
            } else {
                $status = __('voyager::media.error_uploading');
            }
        } else {
            $status = __('voyager::media.uploading_wrong_type');
        }

        $imageParams = config('admin.default_dimensions', ["template" => "dynamic","params" => ["w" => 1000,"h" => 563]]);

        // echo out script that TinyMCE can handle and update the image in the editor
        return "<script> parent.helpers.setImageValue('".Voyager::image($fullFilename, "", $imageParams)."'); </script>";
    }
}
