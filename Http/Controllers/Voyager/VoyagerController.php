<?php

namespace Modules\Admin\Http\Controllers\Voyager;

use TCG\Voyager\Facades\Voyager;
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
}
