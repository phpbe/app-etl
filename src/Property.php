<?php

namespace Be\App\Etl;


class Property extends \Be\App\Property
{

    public $label = 'ETL';
    public $icon = 'el-icon-fa fa-rocket';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

