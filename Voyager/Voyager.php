<?php

namespace Modules\Admin\Voyager;

use Illuminate\Support\Facades\Storage;

class Voyager extends \TCG\Voyager\Voyager
{
    public function image($file, $default = '', $type = '')
    {
        if (!empty($file)) {

            if (!empty($type) && !is_array($type)) {
                $file = $this->getThumbnail($file, $type);
            } elseif(is_array($type)) {
                $tempUrl = str_replace('\\', '/', Storage::disk(config('voyager.storage.disk'))->url($file.$type['params']));
                return str_replace('/storage/', '/'.config('imagecache.route').'/'.$type['template'].'/' , $tempUrl);
            }

            return str_replace('\\', '/', Storage::disk(config('voyager.storage.disk'))->url($file));
        }

        return $default;
    }

    /**
     * Generate thumbnail URL.
     *
     * @param $image
     * @param $type
     *
     * @return string
     */
    public function getThumbnail($image, $type)
    {
        // We need to get extension type ( .jpeg , .png ...)
        $ext = pathinfo($image, PATHINFO_EXTENSION);

        // We remove extension from file name so we can append thumbnail type
        $name = str_replace_last('.'.$ext, '', $image);

        // We merge original name + type + extension
        return $name.'-'.$type.'.'.$ext;
    }

    protected function findVersion()
    {
        if (!is_null($this->version)) {
            return;
        }

        if ($this->filesystem->exists(base_path('composer.lock'))) {
            // Get the composer.lock file
            $file = json_decode(
                $this->filesystem->get(base_path('composer.lock'))
            );

            // Loop through all the packages and get the version of voyager
            foreach ($file->packages as $package) {
                if ($package->name == 'glenwell/admin-module') {
                    $this->version = $package->version;
                    break;
                }
            }
        }
    }
}
