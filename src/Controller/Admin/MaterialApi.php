<?php

namespace Be\App\Etl\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * @BeMenuGroup("素材")
 * @BePermissionGroup("素材")
 */
class MaterialApi extends Auth
{

    /**
     * 采集接口
     *
     * @BeMenu("API接口", icon="bi-bounding-box", ordering="2.3")
     * @BePermission("API接口", ordering="2.3")
     */
    public function config()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $config = Be::getService('App.Etl.Admin.MaterialApi')->getConfig();
        $response->set('config', $config);

        $service = Be::getService('App.Etl.Admin.Material');

        $materialIdNameKeyValues = $service->getIdNameKeyValues();
        $response->set('materialIdNameKeyValues', $materialIdNameKeyValues);

        $materialId = $request->get('material_id', '');
        if (isset($materialIdNameKeyValues[$materialId])) {
            $material = $service->getMaterial($materialId);
            $response->set('material', $material);
        } else {
            $response->set('material', false);
        }

        $response->set('title', 'API接口');
        $response->display();
    }

    /**
     * 采集接口 切换启用状态
     *
     * @BePermission("API接口")
     */
    public function toggleEnable()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $enable = Be::getService('App.Etl.Admin.MaterialApi')->toggleEnable();
            $response->set('success', true);
            $response->set('message', 'API接口开关' . ($enable ? '启用' : '停用') . '成功！');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 采集接口 重设Token
     *
     * @BePermission("API接口")
     */
    public function resetToken()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            Be::getService('App.Etl.Admin.MaterialApi')->resetToken();
            $response->redirect(beAdminUrl('Etl.MaterialApi.config'));
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }


}
