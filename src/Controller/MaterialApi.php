<?php

namespace Be\App\Etl\Controller;

use Be\App\ControllerException;
use Be\Be;

/**
 * 接口
 */
class MaterialApi
{

    public function __contruct()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $serviceMaterialApi = Be::getService('App.Etl.Admin.MaterialApi');
        $materialApiConfig = $serviceMaterialApi->getConfig();

        if ($materialApiConfig->enable === 0) {
            $response->error('素材 API 接口未启用！');
            $response->end();
        }

        $token = $request->get('token', '');
        if ($materialApiConfig->token !== $token) {
            $response->error('素材 API Token 无效！');
            $response->end();
        }
    }

    /**
     * 创建
     *
     * @BeRoute("/etl/material/api/create")
     */
    public function materialCreate()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
    }

    /**
     * 编辑
     *
     * @BeRoute("/etl/material/api/edit")
     */
    public function materialEdit()
    {

    }

    /**
     * 取用
     *
     * @BeRoute("/etl/material/api/fetch")
     */
    public function materialFetch()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
    }



}
