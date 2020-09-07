<?php

namespace Be\App\Etl\Service;

use Be\System\Be;

class ExtractCategory extends \Be\System\Service
{

    public function getIdNameKeyValues()
    {
        return Be::getTable('etl_extract_category')
            ->where('is_delete', 0)
            ->where('is_enable', 1)
            ->orderBy('ordering', 'ASC')
            ->getKeyValues('id', 'name');
    }

}
