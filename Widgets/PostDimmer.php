<?php

namespace Modules\Admin\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use Modules\Admin\Models\Post;

class PostDimmer extends \TCG\Voyager\Widgets\PostDimmer
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
        $count = Post::count();
        $string = trans_choice('voyager::dimmer.post', $count);

        return view('admin::voyager.dimmer', array_merge($this->config, [
            'icon'   => 'icon-article-2',
            'title'  => "{$count} {$string}",
            'text'   => __('voyager::dimmer.post_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'link' => route('voyager.posts.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/2.png'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', app(Post::class));
    }
}
