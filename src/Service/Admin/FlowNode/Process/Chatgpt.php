<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Process;


use Be\App\Etl\Service\Admin\FlowNode\Process;
use Be\App\ServiceException;
use Be\Be;
use Be\Task\TaskException;

class Chatgpt extends Process
{

    public function getItemName(): string
    {
        return 'ChatGPT';
    }


    public function test(array $formDataNode, object $input): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['system_prompt']) || !is_string($formDataNode['item']['system_prompt'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 系统提示语（system_prompt）参数无效！');
        }

        if (!isset($formDataNode['item']['user_prompt']) || !is_string($formDataNode['item']['user_prompt']) || $formDataNode['item']['user_prompt'] === '') {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 用户提示语（user_prompt）参数无效！');
        }

        if (!isset($formDataNode['item']['output_field']) || !is_string($formDataNode['item']['output_field']) || !in_array($formDataNode['item']['output_field'], ['assign', 'custom'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 输出字段（output_field）参数无效！');
        }

        if ($formDataNode['item']['output_field'] === 'assign') {
            if (!isset($formDataNode['item']['output_field_assign']) || !is_string($formDataNode['item']['output_field_assign']) || $formDataNode['item']['output_field_assign'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 输出字段：指定字段（output_field_assign）参数无效！');
            }
        } else {
            if (!isset($formDataNode['item']['output_field_custom']) || !is_string($formDataNode['item']['output_field_custom']) || $formDataNode['item']['output_field_custom'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 输出字段：自定义字段（output_field_custom）参数无效！');
            }
        }

        $messages = $this->formatAiMessages($formDataNode['item']['system_prompt'], $formDataNode['item']['user_prompt'], $input);
        $result = $this->chatCompletion($messages);

        $output = clone $input;
        if ($formDataNode['item']['output_field'] === 'assign') {
            $outputField = $formDataNode['item']['output_field_assign'];
        } else {
            $outputField = $formDataNode['item']['output_field_custom'];
        }

        $output->$outputField = $result;

        return $output;
    }


    public function edit(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_process_chatgpt');

        if (isset($formDataNode['item']['id']) && is_string($formDataNode['item']['id']) && strlen($formDataNode['item']['id']) === 36) {
            try {
                $tupleFlowNodeItem->load($formDataNode['item']['id']);
            } catch (\Throwable $t) {
            }
        }

        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->system_prompt = $formDataNode['item']['system_prompt'];
        $tupleFlowNodeItem->user_prompt = $formDataNode['item']['user_prompt'];
        $tupleFlowNodeItem->output_field = $formDataNode['item']['output_field'];
        $tupleFlowNodeItem->output_field_assign = $formDataNode['item']['output_field_assign'];
        $tupleFlowNodeItem->output_field_custom = $formDataNode['item']['output_field_custom'];
        $tupleFlowNodeItem->output = serialize($formDataNode['item']['output']);

        $tupleFlowNodeItem->update_time = date('Y-m-d H:i:s');

        if ($tupleFlowNodeItem->isLoaded()) {
            $tupleFlowNodeItem->update();
        } else {
            $tupleFlowNodeItem->create_time = date('Y-m-d H:i:s');
            $tupleFlowNodeItem->insert();
        }

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
        $messages = $this->formatAiMessages($flowNode->item->system_prompt, $flowNode->item->user_prompt, $input);
        $result = $this->chatCompletion($messages);

        $output = clone $input;
        if ($flowNode->item->output_field === 'assign') {
            $outputField = $flowNode->item->output_field_assign;
        } else {
            $outputField = $flowNode->item->output_field_custom;
        }

        $output->$outputField = $result;

        return $output;
    }


    /**
     * 格式化提问
     *
     * @param string $systemPrompt
     * @param string $userPrompt
     * @param object $input
     * @return array
     */
    private function formatAiMessages(string $systemPrompt, string $userPrompt, object $input): array
    {
        if ($userPrompt === '') {
            throw new TaskException('用户提示语不能为空！');
        }

        $messages = [];
        if ($systemPrompt !== '') {
            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt,
            ];
        }

        foreach (get_object_vars($input) as $k => $v) {
            $userPrompt = str_replace('{' . $k . '}', $v, $userPrompt);
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userPrompt,
        ];

        return $messages;
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

            if ($hasError) {
                $message = $err->getMessage();

                // 账号额度用完了
                if (strpos($message, 'You exceeded your current quota, please check your plan and billing details.') !== false) {
                    break;
                }
            } else {
                break;
            }

            $times++;

        } while ($times < 5);

        if ($hasError) {
            throw new TaskException('调用OpenAi接口出错：' . $err->getMessage());
        }

        return $answer;
    }

}
