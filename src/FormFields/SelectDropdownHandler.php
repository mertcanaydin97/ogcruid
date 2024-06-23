<?php

namespace Og\Cruid\FormFields;

class SelectDropdownHandler extends AbstractHandler
{
    protected $codename = 'select_dropdown';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('cruid::formfields.select_dropdown', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
