<?php

namespace Modules\Admin\Voyager;

use Illuminate\Support\Facades\Storage;

class Voyager extends \TCG\Voyager\Voyager
{
    public function image($file, $default = '', $params = [])
    {
        if (!empty($file)) {

            if(!empty($params)) {
                $tempUrl = str_replace('\\', '/', Storage::disk(config('voyager.storage.disk'))->url($file.$params['params']));
                return str_replace('/storage/', '/'.config('imagecache.route').'/'.$params['template'].'/' , $tempUrl);
            }

            return str_replace('\\', '/', Storage::disk(config('voyager.storage.disk'))->url($file));
            
        }

        return $default;
    }
}
