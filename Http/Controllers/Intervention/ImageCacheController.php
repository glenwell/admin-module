<?php

namespace Modules\Admin\Http\Controllers\Intervention;

use Closure;
use Intervention\Image\ImageManager;
use Illuminate\Http\Response as IlluminateResponse;
use Config;

class ImageCacheController extends \Intervention\Image\ImageCacheController
{
    /**
     * Get HTTP response of either original image file or
     * template applied file.
     *
     * @param  string $template
     * @param  string $filename
     * @return Illuminate\Http\Response
     */
    public function getResponse($template, $filename)
    {
        return $this->getImage($template, $filename);
    }

    /**
     * Returns corresponding template object from given template name
     *
     * @param  string $template
     * @return mixed
     */
    private function getTemplate($template)
    {
        $template = config("imagecache.templates.{$template}");

        switch (true) {
            // closure template found
            case is_callable($template):
                return $template;

            // filter template found
            case class_exists($template):
                return new $template;
            
            default:
                // template not found
                abort(404);
                break;
        }
    }

    /**
     * Returns full image path from given filename
     *
     * @param  string $filename
     * @return string
     */
    private function getImagePath($filename)
    {
        // find file
        foreach (config('imagecache.paths') as $path) {
            // don't allow '..' in filenames
            $image_path = $path.'/'.str_replace('..', '', $filename);
            if (file_exists($image_path) && is_file($image_path)) {
                // file found
                return $image_path;
            }
        }

        // file not found
        abort(404);
    }

    /**
     * Builds HTTP response from given image data
     *
     * @param  string $content 
     * @return Illuminate\Http\Response
     */
    private function buildResponse($content)
    {
        // define mime type
        $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $content);

        // return http response
        return new IlluminateResponse($content, 200, array(
            'Content-Type' => $mime,
            'Cache-Control' => 'max-age='.(config('imagecache.lifetime')*60).', public',
            'Etag' => md5($content)
        ));
    }
}