<?php

namespace Modules\Admin\Models;

use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Facades\Voyager;

class Page extends \TCG\Voyager\Models\Page
{
    use Resizable;

    public function authorId()
    {
        return $this->belongsTo(Voyager::modelClass('User'), 'author_id', 'id');
    }

}