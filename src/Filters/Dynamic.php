<?php

namespace Modules\Admin\Filters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Dynamic extends BaseFilter implements FilterInterface
{

    public function applyFilter(Image $image)
    {
        $dimensions = $this->dimensions($image);
    
        //Ensure the image is resized to ratio below with aspect ratio maintained
        $image->fit($dimensions->width, $dimensions->height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        },'top');

        $this->addWatermark($image);
        
        return $image;
    }
}