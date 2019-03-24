<?php

namespace Modules\Admin\Http\Controllers\Voyager;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerMediaController as BaseVoyagerMediaController;

class VoyagerMediaController extends BaseVoyagerMediaController
{
    public function index()
    {
        // Check permission
        Voyager::canOrFail('browse_media');

        return Voyager::view('admin::voyager.media.index');
    }
}
