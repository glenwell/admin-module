<?php

namespace Modules\Admin\Http\Controllers\Voyager;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerMenuController as BaseVoyagerMenuController;

class VoyagerMenuController extends BaseVoyagerMenuController
{
    public function builder($id)
    {
        $menu = Voyager::model('Menu')->findOrFail($id);

        $this->authorize('edit', $menu);

        $isModelTranslatable = is_bread_translatable(Voyager::model('MenuItem'));

        return Voyager::view('admin::voyager.menus.builder', compact('menu', 'isModelTranslatable'));
    }
}
