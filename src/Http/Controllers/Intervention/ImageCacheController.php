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
     * Overriden to prevent default intervention filters
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
     * Modified getImage method to allow for image parameters
     *
     * @param  string $template
     * @param  string $params
     * @param  string $filename
     * @return Illuminate\Http\Response
     */
    public function getCustomImageResponse($template, $params, $filename)
    {
        $template = $this->getTemplate($template);
        $path = $this->getImagePath($filename);
        $params = $this->getImageParameters($params);

        // image manipulation based on callback
        $manager = new ImageManager(Config::get('image'));
        $content = $manager->cache(function ($image) use ($template, $path, $params) {

            if ($template instanceof Closure) {
                // build from closure callback template
                $template($image->make($path));
            } else {
                // build from filter template
                $image->make($path)->filter(new $template($params));
            }
            
        }, config('imagecache.lifetime'));

        return $this->buildResponse($content);
    }

    private function getImageParameters($params)
    {
        $filteredParams = [];

        //If we are not requesting the original image...
        if (is_string($params) && $params != "original") {
            
            $paramsArray = explode("-", $params);
            
            //We need at least two parameters to be present
            if(count($paramsArray) > 1) {
                
                foreach ($paramsArray as $param) {
                    //Only width, height, and watermark position allowed
                    if(in_array($param[0], ["w", "h", "p"])) {
                        
                        $arr = str_split($param);
                        //Remove first letter
                        unset($arr[0]);
                        
                        $filteredParams[$param[0]] = intval( implode("",$arr));
                    }
                }

                return $filteredParams;
            }
        }

        return $filteredParams;
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
