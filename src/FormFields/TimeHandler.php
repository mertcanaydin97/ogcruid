<?php

namespace Og\Cruid\FormFields;

class TimeHandler extends AbstractHandler
{
    protected $codename = 'time';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('cruid::formfields.time', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
