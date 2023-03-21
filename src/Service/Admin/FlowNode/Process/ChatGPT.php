<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Process;


use Be\App\Etl\Service\Admin\FlowNode\Process;
use Be\App\ServiceException;
use Be\Be;
use Be\Task\TaskException;

class ChatGPT extends Process
{

    public function getItemName(): string
    {
        return 'ChatGPT';
    }

    /**
     * 编辑数据流
     *
     * @param array $formDataNode 表单数据
     * @param object $input 输入数据
     * @return array 
     * @throws \Throwable
     */
    public function test(array $formDataNode, object $input): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['code']) || !is_string($formDataNode['item']['code']) || $formDataNode['item']['code'] !== '') {
            throw new ServiceException('节点 ' .$formDataNode['index'] . ' 代码（code）参数无效！');
        }

        try {
            $fn = eval('return function(object $input): object {' . $formDataNode['item']['code'] . '};');
            $output = $fn($input);
        } catch (\Throwable $t) {
            throw new ServiceException('节点 ' .$formDataNode['index'] . ' 代码（code）执行出错：' . $t->getMessage());
        }

        return $output;
    }


    /**
     * 插入数据库
     * @param string $flowNodeId
     * @param array $formDataNode
     * @return object
     */
    public function insert(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_process_chatgpt');
        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->code = $formDataNode['item']['code'];
        $tupleFlowNodeItem->output = serialize($formDataNode['item']['output']);
        $tupleFlowNodeItem->create_time = date('Y-m-d H:i:s');
        $tupleFlowNodeItem->update_time = date('Y-m-d H:i:s');
        $tupleFlowNodeItem->insert();
        return $tupleFlowNodeItem->toObject();
    }

    /**
     * 格式化数据库中读取出来的数据
     *
     * @param object $nodeItem
     * @return object
     */
    public function format(object $nodeItem): object
    {
        $nodeItem->output = unserialize($nodeItem->output);
        if ($nodeItem->output === '') {
            $nodeItem->output = false;
        }

        return $nodeItem;
    }

    public function process(object $flowNode, object $input, object $flowLog, object $flowNodeLog): object
    {

        $messages = [];
        $summary = $this->chatCompletion($messages);

        return $summary;
    }



    /**
     * 文本应签
     *
     * @param string $prompt
     * @return string
     * @throws TaskException
     */
    private function chatCompletion(array $messages): string
    {
        $serviceApi = Be::getService('App.Openai.Api');

        $err = null;

        $times = 1;
        do {

            $hasError = false;
            try {
                $answer = $serviceApi->chatCompletion($messages);
            } catch (\Throwable $t) {
                $hasError = true;

                $err = $t;
            }

            if (Be::getRuntime()->isSwooleMode()) {
                \Swoole\Coroutine::sleep(5);
            } else {
                sleep(5);
            }

            if (!$hasError) {
                break;
            }

            $times++;

        } while ($times < 5);

        if ($hasError) {
            throw new TaskException('调用OpenAi接口重试出错超过5次：' . $err->getMessage());
        }

        return $answer;
    }

}
