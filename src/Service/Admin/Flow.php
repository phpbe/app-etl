<?php

namespace Be\App\Etl\Service\Admin;


use Be\App\ServiceException;
use Be\Be;

class Flow
{

    /**
     * 创建数据流
     *
     * @param array $formData 数据流数据
     * @return object
     * @throws \Throwable
     */
    public function create(array $formData): object
    {
        $db = Be::getDb();

        if (!isset($formData['name']) || !is_string($formData['name'])) {
            throw new ServiceException('数据流名称未填写！');
        }

        if (!isset($formData['category_id']) || !is_string($formData['category_id'])) {
            throw new ServiceException('分类未填写！');
        }

        if (!isset($formData['is_enable']) || !is_numeric($formData['is_enable'])) {
            $formData['is_enable'] = 0;
        }

        $formData['is_enable'] = (int)$formData['is_enable'];

        if (!in_array($formData['is_enable'], [0, 1])) {
            $formData['is_enable'] = 0;
        }

        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');

            $tupleFlow = Be::getTuple('etl_flow');
            $tupleFlow->name = $formData['name'];
            $tupleFlow->category_id = $formData['category_id'];
            $tupleFlow->is_enable = $formData['is_enable'];
            $tupleFlow->create_time = $now;
            $tupleFlow->update_time = $now;
            $tupleFlow->insert();

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);
            throw new ServiceException('新建数据流发生异常！');
        }

        return $tupleFlow->toObject();

    }


    /**
     * 编辑数据流
     *
     * @param array $formData 数据流数据
     * @return object
     * @throws \Throwable
     */
    public function edit(array $formData): object
    {
        if (!isset($formData['id']) || !is_string($formData['id']) || strlen($formData['id']) !== 36) {
            throw new ServiceException('参数（id）无效！');
        }

        $flowId = $formData['id'];
        $tupleFlow = Be::getTuple('etl_flow');
        try {
            $tupleFlow->load($flowId);
        } catch (\Throwable $t) {
            throw new ServiceException('数据流（# ' . $flowId . '）不存在！');
        }

        if (!isset($formData['name']) || !is_string($formData['name'])) {
            throw new ServiceException('数据流名称未填写！');
        }

        if (!isset($formData['category_id']) || !is_string($formData['category_id'])) {
            throw new ServiceException('分类未填写！');
        }

        if (!isset($formData['is_enable']) || !is_numeric($formData['is_enable'])) {
            $formData['is_enable'] = 0;
        }

        $formData['is_enable'] = (int)$formData['is_enable'];

        if (!in_array($formData['is_enable'], [0, 1])) {
            $formData['is_enable'] = 0;
        }


        if (!isset($formData['nodes']) || !is_array($formData['nodes'])) {
            throw new ServiceException('数据流节点数据缺失！');
        }

        $i = 0;
        $input = null;
        foreach ($formData['nodes'] as &$formDataNode) {

            if ($i === 0) {
                if ($formDataNode['type'] !== 'input') {
                    throw new ServiceException('未设置理入节点！');
                }
            }

            $service = $this->getNodeItemService($formDataNode['item_type']);

            if ($i === 0) {
                $output = $service->test($formDataNode);
            } else {
                $output = $service->test($formDataNode, $input);
            }

            $input = $output;
            $formDataNode['output'] = $output;

            $i++;
        }
        unset($formDataNode);

        $db = Be::getDb();
        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $tupleFlow->name = $formData['name'];
            $tupleFlow->category_id = $formData['category_id'];
            $tupleFlow->is_enable = $formData['is_enable'];
            $tupleFlow->update_time = $now;
            $tupleFlow->update();

            $sql = 'SELECT * FROM etl_flow_node WHERE flow_id = ?';
            $existsNodes = $db->getObjects($sql, [$flowId]);
            if (count($existsNodes) > 0) {
                foreach ($existsNodes as $existsNode) {
                    $sql = 'DELETE FROM etl_flow_node_' . $existsNode->item_type . ' WHERE flow_node_id = ?';
                    $db->query($sql, [$existsNode->id]);
                }

                $sql = 'DELETE FROM etl_flow_node WHERE flow_id = ?';
                $db->query($sql, [$flowId]);
            }

            foreach ($formData['nodes'] as $formDataNode) {
                $tupleFlowNode = Be::getTuple('etl_flow_node');
                $tupleFlowNode->flow_id = $flowId;
                $tupleFlowNode->index = $formDataNode['index'];
                $tupleFlowNode->type = $formDataNode['type'];
                $tupleFlowNode->item_type = $formDataNode['item_type'];
                $tupleFlowNode->item_id = '';
                $tupleFlowNode->create_time = $now;
                $tupleFlowNode->update_time = $now;
                $tupleFlowNode->insert();

                $service = $this->getNodeItemService($formDataNode['item_type']);

                $tupleFlowNodeItem = $service->insert($tupleFlowNode->id, $formDataNode);

                $tupleFlowNode->item_id = $tupleFlowNodeItem->id;
                $tupleFlowNode->update();
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);
            throw new ServiceException('编辑数据流发生异常！');
        }

        return $tupleFlow->toObject();
    }


    /**
     * 测试数据流
     *
     * @param array $formData 数据流数据
     * @param int $index 节点索引
     * @return object
     * @throws \Throwable
     */
    public function test(array $formData, int $index): object
    {
        $flowId = $formData['id'];
        $tupleFlow = Be::getTuple('etl_flow');
        try {
            $tupleFlow->load($flowId);
        } catch (\Throwable $t) {
            throw new ServiceException('数据流（# ' . $flowId . '）不存在！');
        }

        $flow = $tupleFlow->toObject();

        if (!isset($formData['nodes']) || !is_array($formData['nodes'])) {
            throw new ServiceException('数据流节点数据缺失！');
        }

        $nodes = [];
        $i = 0;
        $input = null;
        foreach ($formData['nodes'] as $formDataNode) {

            if ($i === 0) {
                if ($formDataNode['type'] !== 'input') {
                    throw new ServiceException('未设置理入，无法测试！');
                }
            }

            $node = [];
            $node['index'] = $formDataNode['index'];
            $node['item'] = [];

            if ($i > $index) {

                // 无输入时，直接无输出
                if ($input === false) {

                    $node['item']['output'] = false;
                    $nodes[] = $node;

                } else {

                    // 当前节点之后节点，仅检测，不抛错
                    try {
                        $service = $this->getNodeItemService($formDataNode['item_type']);
                        $output = $service->test($formDataNode, $input);
                    } catch (\Throwable $t) {
                        $output = false;
                    }

                    $input = $output;

                    $node['item']['output'] = $output;
                    $nodes[] = $node;
                }
            } else {

                $service = $this->getNodeItemService($formDataNode['item_type']);

                if ($i === 0) {
                    $output = $service->test($formDataNode);
                } else {
                    $output = $service->test($formDataNode, $input);
                }

                $input = $output;

                $node['item']['output'] = $output;
                $nodes[] = $node;
            }

            $i++;
        }

        $flow->nodes = $nodes;

        return $flow;
    }

    /**
     * 获取数据流
     *
     * @param string $flowId
     * @return object
     */
    public function getFlow(string $flowId): object
    {
        $tupleFlow = Be::getTuple('etl_flow');
        try {
            $tupleFlow->load($flowId);
        } catch (\Throwable $t) {
            throw new ServiceException('数据流（# ' . $flowId . '）不存在！');
        }

        $flow = $tupleFlow->toObject();

        $nodes = Be::getTable('etl_flow_node')
            ->where('flow_id', $flowId)
            ->orderBy('index', 'asc')
            ->getObjects();

        foreach ($nodes as $node) {

            $node->index = (int) $node->index;

            $tupleNodeItem = Be::getTuple('etl_flow_node_' . $node->item_type);
            try {
                $tupleNodeItem->load($node->item_id);
            } catch (\Throwable $t) {
                throw new ServiceException('数据流 子项（# ' . $node->item_id . '）不存在！');
            }

            $service = $this->getNodeItemService($node->item_type);
            $node->item = $service->format($tupleNodeItem->toObject());
        }

        $flow->nodes = $nodes;

        return $flow;
    }

    private function getNodeItemService($nodeItemType)
    {
        $arr = explode('_', $nodeItemType);
        $serviceName = '\\App\\Etl\\Admin\\FlowNode\\' . ucfirst($arr[0]) . '\\' . ucfirst($arr[1]);
        return new $serviceName();
    }

    public function getIdNameKeyValues()
    {
        return Be::getTable('etl_flow')
            ->where('is_delete', 0)
            ->where('is_enable', 1)
            ->getKeyValues('id', 'name');
    }


    public function getDsTypeKeyValues()
    {
        return [
            'table' => '表',
            'sql' => 'SQL语句',
        ];
    }

    public function getFieldMappingKeyValues()
    {
        return [
            'mapping' => '字段映射',
            'code' => '代码处理',
        ];
    }

    public function getBreakpointKeyValues()
    {
        return [
            'full' => '全量',
            'breakpoint' => '有断点',
        ];
    }

    public function getBreakpointStepKeyValues()
    {
        return [
            '1_HOUR' => '一小时',
            '1_DAY' => '一天',
            '1_MONTH' => '一个月',
        ];
    }

}
