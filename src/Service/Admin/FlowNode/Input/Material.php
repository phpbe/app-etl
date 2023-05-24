<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Input;


use Be\App\Etl\Service\Admin\FlowNode\Input;
use Be\App\ServiceException;
use Be\Be;
use Be\Db\Driver;
use Be\Util\Time\Datetime;

class Material extends Input
{


    public function getItemName(): string
    {
        return '素材';
    }

    /**
     * 编辑数据流
     *
     * @param array $formDataNode ['item'] 表单数据
     * @return array
     * @throws \Throwable
     */
    public function test(array $formDataNode): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['material_id']) || !is_string($formDataNode['item']['material_id']) || strlen($formDataNode['item']['material_id']) !== 36) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 素材（material_id）参数无效！');
        }


        if (!isset($formDataNode['item']['breakpoint']) || !is_string($formDataNode['item']['breakpoint']) || !in_array($formDataNode['item']['breakpoint'], ['full', 'breakpoint'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 断点类型（breakpoint）参数无效！');
        }

        if ($formDataNode['item']['breakpoint'] === 'breakpoint') {

            if (!isset($formDataNode['item']['breakpoint_field']) || !is_string($formDataNode['item']['breakpoint_field']) || $formDataNode['item']['breakpoint_field'] === '' || !in_array($formDataNode['item']['breakpoint_field'], ['create_time', 'update_time'])) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 断点字段（breakpoint_field）参数无效！');
            }

            if (!isset($formDataNode['item']['breakpoint_time']) || !is_string($formDataNode['item']['breakpoint_time']) || !strtotime($formDataNode['item']['breakpoint_time'])) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 断点时间（breakpoint_time）参数无效！');
            }

            if (!isset($formDataNode['item']['breakpoint_step']) || !is_string($formDataNode['item']['breakpoint_step']) || !in_array($formDataNode['item']['breakpoint_step'], ['1_HOUR', '1_DAY', '1_MONTH'])) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 断点递增量（breakpoint_step）参数无效！');
            }

            if (!isset($formDataNode['item']['breakpoint_offset']) || !is_numeric($formDataNode['item']['breakpoint_offset'])) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 断点向前编移量（breakpoint_offset）无效！');
            }
            $formDataNode['item']['breakpoint_offset'] = (int) $formDataNode['item']['breakpoint_offset'];

        } else {
            $formDataNode['item']['breakpoint_field'] = '';
            $formDataNode['item']['breakpoint_time'] = '1970-01-02 00:00:00';
            $formDataNode['item']['breakpoint_step'] = '1_HOUR';
            $formDataNode['item']['breakpoint_offset'] = 0;
        }

        $db = Be::getDb();

        $t = time();

        $breakpointEnd = null;
        $tBreakpointEnd = null;

        $where = '';
        $where1 = ' WHERE ' . $db->quoteKey('material_id') . '=' . $db->quoteValue($formDataNode['item']['material_id']);
        $where2 = '';

        if ($formDataNode['item']['breakpoint'] === 'breakpoint') { // 按断点同步
            $breakpointStart = $formDataNode['item']['breakpoint_time'];
            $tBreakpointStart = strtotime($breakpointStart);

            if ($tBreakpointStart > $t) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 断点设置已超过当前时间，程序中止！');
            }

            switch ($formDataNode['item']['breakpoint_step']) {
                case '1_HOUR':
                    $tBreakpointEnd = $tBreakpointStart + 3600;
                    if ($tBreakpointEnd > $t) {
                        $tBreakpointEnd = $t;
                    }
                    $breakpointEnd = date('Y-m-d H:i:s', $tBreakpointEnd);
                    break;
                case '1_DAY':
                    $tBreakpointEnd = $tBreakpointStart + 86400;
                    if ($tBreakpointEnd > $t) {
                        $tBreakpointEnd = $t;
                    }
                    $breakpointEnd = date('Y-m-d H:i:s', $tBreakpointEnd);
                    break;
                case '1_MONTH':
                    $breakpointEnd = Datetime::getNextMonth($breakpointStart);
                    $tBreakpointEnd = strtotime($breakpointEnd);
                    if ($tBreakpointEnd > $t) {
                        $tBreakpointEnd = $t;
                        $breakpointEnd = date('Y-m-d H:i:s', $tBreakpointEnd);
                    }
                    break;
            }

            if ($formDataNode['item']['breakpoint_offset'] > 0) {
                $tBreakpointStart -= $formDataNode['item']['breakpoint_offset'];
                $breakpointStart = date('Y-m-d H:i:s', $tBreakpointStart);
            }

            $where2 = ' AND ';
            $where2 .= $db->quoteKey($formDataNode['item']['breakpoint_field']) . '>=' . $db->quoteValue($breakpointStart);
            $where2 .= ' AND ';
            $where2 .= $db->quoteKey($formDataNode['item']['breakpoint_field']) . '<' . $db->quoteValue($breakpointEnd);
        }

        $where = $where1 . $where2;

        $sql = 'SELECT COUNT(*) FROM ' . $db->quoteKey('etl_material_item') . $where;

        $total = (int) $db->getValue($sql);
        if ($total === 0) { // 断点无数量里，移除断点条件
            $where = $where1;

            $sql = 'SELECT COUNT(*) FROM ' . $db->quoteKey('etl_material_item') . $where;
            $total = (int)$db->getValue($sql);
        }

        if ($total === 0) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 未读取到符合条件的数据，无法完成验证！');
        }

        $sql = 'SELECT * FROM ' . $db->quoteKey('etl_material_item') . $where . ' LIMIT 1';

        $material = $db->getObject($sql);

        $this->material = Be::getService('App.Etl.Admin.Material')->getMaterial($formDataNode['item']['material_id']);

        return $this->formatMaterialItem($material);
    }

    public function edit(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_input_material');

        if (isset($formDataNode['item']['id']) && is_string($formDataNode['item']['id']) && strlen($formDataNode['item']['id']) === 36) {
            try {
                $tupleFlowNodeItem->load($formDataNode['item']['id']);
            } catch (\Throwable $t) {
            }
        }

        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->material_id = $formDataNode['item']['material_id'];
        $tupleFlowNodeItem->breakpoint = $formDataNode['item']['breakpoint'];
        $tupleFlowNodeItem->breakpoint_field = $formDataNode['item']['breakpoint_field'];
        $tupleFlowNodeItem->breakpoint_time = $formDataNode['item']['breakpoint_time'];
        $tupleFlowNodeItem->breakpoint_step = $formDataNode['item']['breakpoint_step'];
        $tupleFlowNodeItem->breakpoint_offset = $formDataNode['item']['breakpoint_offset'];
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

    private $breakpointStart = null;
    private $breakpointEnd = null;
    private ?object $material = null;

    public function start(object $flowNode, object $flowLog, object $flowNodeLog)
    {

        $flowNode->item->breakpoint_offset = (int)$flowNode->item->breakpoint_offset;

        $t = time();

        if ($flowNode->item->breakpoint === 'breakpoint') { // 按断点同步
            $breakpointStart = $flowNode->item->breakpoint_time;
            $tBreakpointStart = strtotime($breakpointStart);

            if ($tBreakpointStart > $t) {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . '断点设置已超过当前时间，程序中止！');
            }

            switch ($flowNode->item->breakpoint_step) {
                case '1_HOUR':
                    $tBreakpointEnd = $tBreakpointStart + 3600;
                    if ($tBreakpointEnd > $t) {
                        $tBreakpointEnd = $t;
                    }
                    $breakpointEnd = date('Y-m-d H:i:s', $tBreakpointEnd);
                    break;
                case '1_DAY':
                    $tBreakpointEnd = $tBreakpointStart + 86400;
                    if ($tBreakpointEnd > $t) {
                        $tBreakpointEnd = $t;
                    }
                    $breakpointEnd = date('Y-m-d H:i:s', $tBreakpointEnd);
                    break;
                case '1_MONTH':
                    $breakpointEnd = Datetime::getNextMonth($breakpointStart);
                    $tBreakpointEnd = strtotime($breakpointEnd);
                    if ($tBreakpointEnd > $t) {
                        $tBreakpointEnd = $t;
                        $breakpointEnd = date('Y-m-d H:i:s', $tBreakpointEnd);
                    }
                    break;
            }

            if ($flowNode->item->breakpoint_offset > 0) {
                $tBreakpointStart -= $flowNode->item->breakpoint_offset;
                $breakpointStart = date('Y-m-d H:i:s', $tBreakpointStart);
            }

            $this->breakpointStart = $breakpointStart;
            $this->breakpointEnd = $breakpointEnd;
        }

        $this->material = Be::getService('App.Etl.Admin.Material')->getMaterial($flowNode->item->material_id);
    }

    public function getTotal(object $flowNode): int
    {
        $db = Be::getDb();

        $where = ' WHERE ' . $db->quoteKey('material_id') . '=' . $db->quoteValue($flowNode->item->material_id);

        if ($flowNode->item->breakpoint === 'breakpoint') { // 按断点同步
            $where = '  AND ';
            $where .= $db->quoteKey($flowNode->item->breakpoint_field) . '>=' . $db->quoteValue($this->breakpointStart);
            $where .= ' AND ';
            $where .= $db->quoteKey($flowNode->item->breakpoint_field) . '<' . $db->quoteValue($this->breakpointEnd);
        }

        $sql = 'SELECT COUNT(*) FROM ' . $db->quoteKey('etl_material_item') . $where;

        return (int)$db->getValue($sql);
    }

    public function process(object $flowNode, object $flowLog, object $flowNodeLog): \Generator
    {
        $db = Be::getDb();

        $where = ' WHERE ' . $db->quoteKey('material_id') . '=' . $db->quoteValue($flowNode->item->material_id);

        if ($flowNode->item->breakpoint === 'breakpoint') { // 按断点同步
            $where = '  AND ';
            $where .= $db->quoteKey($flowNode->item->breakpoint_field) . '>=' . $db->quoteValue($this->breakpointStart);
            $where .= ' AND ';
            $where .= $db->quoteKey($flowNode->item->breakpoint_field) . '<' . $db->quoteValue($this->breakpointEnd);
        }

        $sql = 'SELECT * FROM ' . $db->quoteKey('etl_material_item') . $where;

        $materials = $db->getYieldObjects($sql);
        foreach ($materials as $material) {
            yield $this->formatMaterialItem($material);
        }
    }


    public function finish(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        if ($flowNode->item->breakpoint === 'breakpoint') { // 按断点同步
            $obj = new \stdClass();
            $obj->id = $flowNode->item->id;
            $obj->breakpoint_time = $this->breakpointEnd;
            $obj->update_time = date('Y-m-d H:i:s');

            $db = Be::newDb();
            $db->update('etl_flow_node_input_material', $obj);
        }
    }

    /**
     * 格式化素材
     *
     * @return object
     */
    private function formatMaterialItem($materialItem): object
    {
        $m = new \stdClass();
        $m->id = $materialItem->id;
        $m->material_id = $materialItem->material_id;
        $m->unique_key = $materialItem->unique_key;

        $data = unserialize($materialItem->data);
        foreach ($this->material->fields as $field) {
            $fieldName = $field->name;
            if (isset($data[$fieldName])) {
                $m->$fieldName = $data[$fieldName];
            } else {
                $m->$fieldName = $field->default;
            }
        }

        $m->create_time = $materialItem->create_time;
        $m->update_time = $materialItem->update_time;

        return $m;
    }

}
