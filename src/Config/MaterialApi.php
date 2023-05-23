<?php
namespace Be\App\Etl\Config;

/**
 * @BeConfig("素材接口")
 */
class MaterialApi
{

    /**
     * @BeConfigItem("是否启用接口",
     *     description="启用后，可以将通过接口写入或读取素才",
     *     driver="FormItemSwitch"
     * )
     */
    public int $enable = 0;

    /**
     * @BeConfigItem("接口密钥",
     *     description="密码用于识别已授权的访问，附加到网址中传输，为了系统安全，请妥善保管。",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.enable === 1']];"
     * )
     */
    public string $token = '';


}

