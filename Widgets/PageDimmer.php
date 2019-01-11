<?php

namespace Modules\Admin\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use Modules\Admin\Models\Page;

class PageDimmer extends \TCG\Voyager\Widgets\PageDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = Page::count();
        $string = trans_choice('voyager::dimmer.page', $count);

        return view('admin::voyager.dimmer', array_merge($this->config, [
            'icon'   => 'icon-book-3',
            'title'  => "{$count} {$string}",
            'text'   => __('voyager::dimmer.page_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'link' => route('voyager.pages.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/3.png'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', app(Page::class));
    }
}
