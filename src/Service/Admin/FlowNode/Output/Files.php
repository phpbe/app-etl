<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Output;


use Be\App\Etl\Service\Admin\FlowNode\Output;
use Be\App\ServiceException;
use Be\Be;

class Files extends Output
{

    /**
     * 编辑数据流
     *
     * @param array $formDataNode 表单数据
     * @param object $input 输入数据
     * @return object
     * @throws \Throwable
     */
    public function test(array $formDataNode, object $input): object
    {

        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['name']) || !is_string($formDataNode['item']['name']) || !in_array($formDataNode['item']['name'], ['template', 'code'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 文件名处理方式（name）参数无效！');
        }

        $output = new \stdClass();

        if ($formDataNode['item']['name'] === 'template') {

            if (!isset($formDataNode['item']['name_template']) || !is_string($formDataNode['item']['name_template']) || strlen($formDataNode['item']['name_template']) === 0) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 文件名称模板（name_template）参数无效！');
            }

            $name = $formDataNode['item']['name_template'];
            foreach ((array)$input as $k => $v) {
                $name = str_replace('{' . $k . '}', $v, $name);
            }
            $output->name = $name;

        } else {

            if (!isset($formDataNode['item']['name_code']) || !is_string($formDataNode['item']['name_code']) || strlen($formDataNode['item']['name_code']) === 0) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 文件名代码处理（name_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): string {' . $formDataNode['item']['name_code'] . '};');
                $output->name = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 文件名代码处理（name_code）执行出错：' . $t->getMessage());
            }
        }

        if (!isset($formDataNode['item']['content']) || !is_string($formDataNode['item']['content']) || !in_array($formDataNode['item']['content'], ['template', 'code'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 内容处理方式（content）参数无效！');
        }

        if ($formDataNode['item']['content'] === 'template') {

            if (!isset($formDataNode['item']['content_template']) || !is_string($formDataNode['item']['content_template']) || strlen($formDataNode['item']['content_template']) === 0) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 文件内容模板（content_template）参数无效！');
            }

            $content = $formDataNode['item']['content_template'];
            foreach ((array)$input as $k => $v) {
                $content = str_replace('{' . $k . '}', $v, $content);
            }
            $output->content = $content;

        } else {

            if (!isset($formDataNode['item']['content_code']) || !is_string($formDataNode['item']['content_code']) || strlen($formDataNode['item']['content_code']) === 0) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 文件内容代码处理（content_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): string {' . $formDataNode['item']['content_code'] . '};');
                $output->content = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 文件内容代码处理（content_code）执行出错：' . $t->getMessage());
            }
        }

        if (count((array)$output) === 0) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 输出数据为空！');
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
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_output_files');
        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->name = $formDataNode['item']['name'];
        $tupleFlowNodeItem->name_template = $formDataNode['item']['name_template'];
        $tupleFlowNodeItem->name_code = $formDataNode['item']['name_code'];
        $tupleFlowNodeItem->content = $formDataNode['item']['content'];
        $tupleFlowNodeItem->content_template = $formDataNode['item']['content_template'];
        $tupleFlowNodeItem->content_code = $formDataNode['item']['content_code'];
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


    /**
     * 计划任务处理数据
     *
     * @param object $flowNode 数据流节点
     * @param object $input 输入
     * @return object 输出
     * @throws \Throwable
     */
    public function process(object $flowNode, object $input): object
    {

    }


}
