<?php

namespace Modules\Admin\Models;

use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Facades\Voyager;

class Post extends \TCG\Voyager\Models\Post
{
    use Resizable;

    public function author()
    {
        return $this->belongsTo(Voyager::modelClass('User'), 'author_id', 'id');
    }

}
