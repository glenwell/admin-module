<?php

namespace Modules\Admin\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\ImageManagerStatic as Watermark;
use Intervention\Image\Image;

abstract class BaseFilter implements FilterInterface
{
    private $params;

    public function __construct($params = null)
    {
        $this->params = is_array($params) ? (object) $params : (object) [];
    }

    public function dimensions(Image $image)
    {
        return (object) [
            'width' => $this->getDimensions($image, "width"),
            'height' => $this->getDimensions($image, "height"),
        ];
    }

    public function addWatermark(Image $image)
    {
        //Watermark setting accepted, watermark location set, watermark exists
        if(config('admin.images.watermark.enabled', false)) {
            $dimensions = $this->dimensions($image);
            $minWidth = config('admin.images.watermark.min_width', 200);
            $minHeight = config('admin.images.watermark.min_height', 200);

            //Image large enough to add watermark
            if($dimensions->width >= $minWidth && $dimensions->height >= $minHeight) {
                
                $file = config('admin.images.watermark.path', public_path('vendor/admin/assets/images/watermark.png'));

                if(file_exists($file)) {
                    $watermark = Watermark::make($file);

                    $largerSide = $dimensions->width > $dimensions->height ? $dimensions->width : $dimensions->height;

                    //Calculate dimension of watermark
                    $waterMarkDimension = $largerSide * 0.1875;
                    //Calculate offset of image
                    $offset = floor($largerSide * 0.0125);
                    //Resize image accordingly
                    $watermark->resize($waterMarkDimension, $waterMarkDimension, function ($constraint) {
                        $constraint->upsize();
                    });

                    $position = $this->getWatermarkPosition();
                    
                    //Add watermark to the image to be saved
                    $image->insert($watermark, $position, $offset, $offset);
                }

                return $image;
            }
        }
    }

    /**
     * Get the width and height of the image
     *
     * @param  \Intervention\Image\Image  $image
     * @param string $dimension
     * @return \Intervention\Image\Image
     */
    private function getDimensions(Image $image, string $dimension)
    {
        //Determine whether height or width
        $method = $dimension == 'height' ? 'height' : 'width';
        
        //If width or height is set, proceed
        if(isset($this->params->{$method[0]})) {
            
            $side = $this->params->{$method[0]};
            
            //Side must be numeric, not null, and less than original image dimension
            if(is_numeric($side) && !is_null($side) && $side > 0 && $side <= $image->{$method}()) {
                return $side;
            } else {
                return $image->{$method}();
            }
        } else {
            return $image->{$method}();
        }
    }

    private function getWatermarkPosition()
    {
        $options = [
            'top-left',
            'top',
            'top-right',
            'left',
            'center',
            'right',
            'bottom-left',
            'bottom',
            'bottom-right'
        ];

        if(isset($this->params->p)) {
            
            $position = $this->params->p;
            
            //Position must be numeric, not null, and less than 8 elements in the options
            if(is_numeric($position) && !is_null($position) && ($position >= 0 && $position <= 8) ){
                return $options[$position];
            } else {
                return $options[config('admin.images.watermark.default_position', 8)];
            }
        } else {
            return $options[config('admin.images.watermark.default_position', 8)];
        }
    }
}