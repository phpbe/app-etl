<?php

namespace Be\App\Etl;


class Property extends \Be\App\Property
{

    public string $label = 'ETL';
    public string $icon = 'bi-box-arrow-right';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

