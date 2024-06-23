<?php

namespace Og\Cruid\Actions;

class RestoreAction extends AbstractAction
{
    public function getTitle()
    {
        return __('cruid::generic.restore');
    }

    public function getIcon()
    {
        return 'cruid-trash';
    }

    public function getPolicy()
    {
        return 'restore';
    }

    public function getAttributes()
    {
        return [
            'class'   => 'btn btn-sm btn-success pull-right restore',
            'data-id' => $this->data->{$this->data->getKeyName()},
            'id'      => 'restore-'.$this->data->{$this->data->getKeyName()},
        ];
    }

    public function getDefaultRoute()
    {
        return route('cruid.'.$this->dataType->slug.'.restore', $this->data->{$this->data->getKeyName()});
    }
}
