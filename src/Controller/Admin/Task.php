<?php

namespace Be\App\Etl\Controller\Admin;

use Be\Be;
use Be\Request;
use Be\Response;

/**
 * Class Task
 * @package App\Etl\Controller
 *
 * @BePermissionGroup("计划任务")
 */
class Task
{

    /**
     * 执行计划任务调度
     * @BePermission("*")
     */
    public function run()
    {
        // 抽取任务
        $extractTasks = Be::newTable('etl_extract')
            ->where('is_delete', 0)
            ->where('is_enable', 1)
            ->where('schedule', '!=', '')
            ->getObjects();
        //print_r($extractTasks);

        $t = time();
        foreach ($extractTasks as $extractTask) {
            $url = beUrl('Etl.Task.runExtract', ['id' => $extractTask->id, 't' => $t]);
            echo $url . '<br>';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_exec($curl);
            curl_close($curl);
        }

        echo '-';
    }


    /**
     * 检查超时的任务
     * @BePermission("*")
     */
    public function checkExpiredExtractLog()
    {
        $configExtract = Be::getConfig('Etl.Extract');
        if ($configExtract->timeout > 0) {
            $extractTaskLogs = Be::newTable('etl_extract_log')
                ->where('status', 1)
                ->where('update_time', '<', date('Y-m-d H:i:s', time() - $configExtract->timeout))
                ->getObjects();
            if (count($extractTaskLogs) > 0) {
                $db = Be::getDb();
                foreach ($extractTaskLogs as $extractTaskLog) {
                    $db->update('etl_extract_log', ['id' => $extractTaskLog->id, 'status' => -1, 'message' => '执行超过 ' . $configExtract->timeout . '秒未更新！'], 'id');
                }
            }
        }
        echo '-';
    }


    /**
     * 抽取计划任务
     *
     * @BePermission("*")
     */
    public function runExtract()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1536M');
        ini_set('memory_limit', '2g');
        ini_set('memory_limit', '4g');
        ini_set('memory_limit', '8g');
        ignore_user_abort(true);
        session_write_close();
        header("Connection: close");
        header("HTTP/1.1 200 OK");
        ob_implicit_flush();

        $id = Request::get('id', 0);
        if (!$id) {
            echo '参数错误！';
            exit;
        }

        $t = Request::get('t', time());
        $manual = Request::get('manual', 0);
        try {
            Be::getService('Etl.Task')->runExtract($id, $t, $manual);
            echo '-';
        } catch (\Exception $e) {
            echo '#' . $e->getCode() . ' : ' . $e->getMessage();
            exit;
        }
    }

    /**
     * 手工启动抽取任务
     *
     * @BePermission("手运运行抽取")
     */
    public function manualRunExtract()
    {
        $postData = Request::json();
        if ($postData) {
            if (isset($postData['row']['id']) && $postData['row']['id']) {
                $id = $postData['row']['id'];
                $url = beUrl('Etl.Task.runExtract', ['id' => $id, 'manual' => 1]);
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HEADER, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_TIMEOUT, 1);
                curl_exec($curl);
                curl_close($curl);
                Response::set('success', true);
                Response::set('message', '手工启动抽取任务成功！');
                Response::set('url', $url);
                Response::json();
            }
        }

        Response::set('success', false);
        Response::set('message', '参数错误！');
        Response::json();
    }

}
