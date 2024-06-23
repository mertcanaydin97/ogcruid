<?php

namespace Og\Cruid\Actions;

class ViewAction extends AbstractAction
{
    public function getTitle()
    {
        return __('cruid::generic.view');
    }

    public function getIcon()
    {
        return 'cruid-eye';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-warning pull-right view',
        ];
    }

    public function getDefaultRoute()
    {
        return route('cruid.'.$this->dataType->slug.'.show', $this->data->{$this->data->getKeyName()});
    }
}
