<?php

namespace Og\Cruid\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Og\Cruid\Facades\Cruid;

class PageDimmer extends BaseDimmer
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
        $count = Cruid::model('Page')->count();
        $string = trans_choice('cruid::dimmer.page', $count);

        return view('cruid::dimmer', array_merge($this->config, [
            'icon'   => 'cruid-file-text',
            'title'  => "{$count} {$string}",
            'text'   => __('cruid::dimmer.page_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => __('cruid::dimmer.page_link_text'),
                'link' => route('cruid.pages.index'),
            ],
            'image' => cruid_asset('images/widget-backgrounds/03.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', Cruid::model('Page'));
    }
}
