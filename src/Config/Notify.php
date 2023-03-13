<?php
namespace Be\App\Etl\Config;

/**
 * @BeConfig("异常通知")
 */
class Notify
{
    /**
     * @BeConfigItem("发送邮件", driver="FormItemSwitch")
     */
    public int $mail = 0;

    /**
     * @BeConfigItem("收件人邮箱",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.mail==1']];")
     */
    public string $toMail = '';


    /**
     * @BeConfigItem("钉钉机器人", driver="FormItemSwitch")
     */
    public int $dingTalkRobot = 0;

    /**
     * @BeConfigItem("钉钉机器人Access Token",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.dingTalkRobot==1']];")
     */
    public string $dingTalkRobotToken = '';

    /**
     * @BeConfigItem("钉钉机器人密钥",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.dingTalkRobot==1']];")
     */
    public string $dingTalkRobotSecret = '';

}
