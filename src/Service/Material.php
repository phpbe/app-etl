<?php

namespace Be\App\Etl\Service;

use Be\App\ServiceException;
use Be\Be;

class Material
{

    /**
     * 从REDIS 素材
     *
     * @param string $materialId 素材ID
     * @return object
     */
    public function getMaterial(string $materialId)
    {
        $cache = Be::getCache();

        $key = 'App:Etl:Material:' . $materialId;
        $material = $cache->get($key);

        if (!$material) {
            try {
                $material = $this->getMaterialFromDb($materialId);
            } catch (\Throwable $t) {
                $material = '-1';
            }

            $cache->set($key, $material, 600);
        }

        if ($material === '-1') {
            throw new ServiceException('素材 #' . $materialId . ' 不存在！');
        }

        return $material;
    }


    /**
     * 获取素材
     *
     * @param string $materialId 素材ID
     * @return object
     */
    public function getMaterialFromDb(string $materialId): object
    {
        $tupleMaterial = Be::getTuple('etl_material');
        try {
            $tupleMaterial->load($materialId);
        } catch (\Throwable $t) {
            throw new ServiceException('素材 #' . $materialId . ' 不存在！');
        }

        $material = $tupleMaterial->toObject();

        $fields = unserialize($material->fields);

        /*
        foreach ($fields as $field) {
        }
        */

        $material->fields = $fields;

        return $material;
    }


}
