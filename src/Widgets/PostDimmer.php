<?php

namespace Og\Cruid\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Og\Cruid\Facades\Cruid;

class PostDimmer extends BaseDimmer
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
        $count = Cruid::model('Post')->count();
        $string = trans_choice('cruid::dimmer.post', $count);

        return view('cruid::dimmer', array_merge($this->config, [
            'icon'   => 'cruid-news',
            'title'  => "{$count} {$string}",
            'text'   => __('cruid::dimmer.post_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => __('cruid::dimmer.post_link_text'),
                'link' => route('cruid.posts.index'),
            ],
            'image' => cruid_asset('images/widget-backgrounds/02.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', Cruid::model('Post'));
    }
}
