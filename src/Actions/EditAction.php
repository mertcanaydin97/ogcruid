<?php

namespace Og\Cruid\Actions;

class EditAction extends AbstractAction
{
    public function getTitle()
    {
        return __('cruid::generic.edit');
    }

    public function getIcon()
    {
        return 'cruid-edit';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary pull-right edit',
        ];
    }

    public function getDefaultRoute()
    {
        return route('cruid.'.$this->dataType->slug.'.edit', $this->data->{$this->data->getKeyName()});
    }
}
