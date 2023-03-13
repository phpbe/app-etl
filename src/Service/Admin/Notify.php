<?php

namespace Be\App\Etl\Service\Admin;


use Be\Be;

class Notify
{

    public function mail($message) {
        $config = Be::getConfig('App.Etl.Notify');
        if (!$config->mail) {
            return false;
        }

        return Be::getService('App.System.Mail')
            ->subject('计划任务发生异常')
            ->body($message)
            ->to($config->toEmail)
            ->send();
    }

    public function dingTalkRobot($message) {

        $config = Be::getConfig('App.Etl.Notify');
        if (!$config->dingTalkRobot) {
            return false;
        }

        $data = [
            'msgtype' => 'text',
            'text' => [
                'content' => $message
            ]
        ];

        $url = 'https://oapi.dingtalk.com/robot/send?access_token=' . $config->dingTalkRobotToken;
        $secret = $config->dingTalkRobotSecret;

        $timestamp = round(microtime(1) * 1000, 0);
        $sign = urlencode(base64_encode(hash_hmac('sha256', $timestamp . "\n" . $secret, $secret, true)));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '&timestamp='.$timestamp.'&sign='.$sign);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }


}