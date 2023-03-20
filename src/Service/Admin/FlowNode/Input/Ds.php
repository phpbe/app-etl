<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Input;


use Be\App\Etl\Service\Admin\FlowNode\Input;
use Be\App\ServiceException;
use Be\Be;
use Be\Util\Time\Datetime;

class Ds extends Input
{

    /**
     * 编辑数据流
     *
     * @param array $formDataNode['item'] 表单数据
     * @return array 
     * @throws \Throwable
     */
    public function test(array $formDataNode): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['ds_id']) || !is_string($formDataNode['item']['ds_id']) || strlen($formDataNode['item']['ds_id']) !== 36) {
            throw new ServiceException('节点 ' .$formDataNode['index'] . ' 数据源（ds_id）参数无效！');
        }

        if (!isset($formDataNode['item']['ds_type']) || !is_string($formDataNode['item']['ds_type']) || !in_array($formDataNode['item']['ds_type'], ['table', 'sql'])) {
            throw new ServiceException('节点 ' .$formDataNode['index'] . ' 类型（ds_type）参数无效！');
        }

        if ($formDataNode['item']['ds_type'] === 'table') {
            if (!isset($formDataNode['item']['ds_table']) || !is_string($formDataNode['item']['ds_table']) || strlen($formDataNode['item']['ds_table']) === 0) {
                throw new ServiceException('节点 ' .$formDataNode['index'] . ' 数据表（ds_table）参数无效！');
            }
            $formDataNode['item']['ds_sql'] = '';
        } else {
            if (!isset($formDataNode['item']['ds_sql']) || !is_string($formDataNode['item']['ds_sql']) || strlen($formDataNode['item']['ds_sql']) === 0) {
                throw new ServiceException('节点 ' .$formDataNode['index'] . ' SQL（ds_sql）参数无效！');
            }
            $formDataNode['item']['ds_table'] = '';
        }

        if (!isset($formDataNode['item']['breakpoint']) || !is_string($formDataNode['item']['breakpoint']) || !in_array($formDataNode['item']['breakpoint'], ['full', 'breakpoint'])) {
            throw new ServiceException('节点 ' .$formDataNode['index'] . ' 断点类型（breakpoint）参数无效！');
        }

        if ($formDataNode['item']['breakpoint'] === 'breakpoint') {

            if (!isset($formDataNode['item']['breakpoint_field']) || !is_string($formDataNode['item']['breakpoint_field']) || strlen($formDataNode['item']['breakpoint_field']) === 0) {
                throw new ServiceException('节点 ' .$formDataNode['index'] . ' 断点字段（breakpoint_field）参数无效！');
            }

            if (!isset($formDataNode['item']['breakpoint_time']) || !is_string($formDataNode['item']['breakpoint_time']) || !strtotime($formDataNode['item']['breakpoint_time'])) {
                throw new ServiceException('节点 ' .$formDataNode['index'] . ' 断点时间（breakpoint_time）参数无效！');
            }

            if (!isset($formDataNode['item']['breakpoint_step']) || !is_string($formDataNode['item']['breakpoint_step']) || !in_array($formDataNode['item']['breakpoint_step'], ['1_HOUR', '1_DAY', '1_MONTH'])) {
                throw new ServiceException('节点 ' .$formDataNode['index'] . ' 断点递增量（breakpoint_step）参数无效！');
            }

            if (!isset($formDataNode['item']['breakpoint_offset']) || !is_numeric($formDataNode['item']['breakpoint_offset'])) {
                throw new ServiceException('节点 ' .$formDataNode['index'] . ' 断点向前编移量（breakpoint_offset）无效！');
            }
            $formDataNode['item']['breakpoint_offset'] = (int) $formDataNode['item']['breakpoint_offset'];

        } else {
            $formDataNode['item']['breakpoint_field'] = '';
            $formDataNode['item']['breakpoint_time'] = '1970-01-02 00:00:00';
            $formDataNode['item']['breakpoint_step'] = '1_HOUR';
            $formDataNode['item']['breakpoint_offset'] = 0;
        }

        $db = Be::getService('App.Etl.Admin.Ds')->newDb($formDataNode['item']['ds_id']);

        $t = time();

        $breakpointEnd = null;
        $tBreakpointEnd = null;
        $where = '';
        if ($formDataNode['item']['breakpoint'] === 'breakpoint') { // 按断点同步
            $breakpointStart = $formDataNode['item']['breakpoint_time'];
            $tBreakpointStart = strtotime($breakpointStart);

            if ($tBreakpointStart > $t) {
                throw new ServiceException('节点 ' .$formDataNode['index'] . ' 断点设置已超过当前时间，程序中止！');
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

            if ($db instanceof \Be\Db\Driver\Oracle) {
                $where = ' WHERE ';
                $where .= $db->quoteKey($formDataNode['item']['breakpoint_field']) . '>=to_timestamp(\'' . $breakpointStart . '\', \'yyyy-mm-dd hh24:mi:ss\')';
                $where .= ' AND ';
                $where .= $db->quoteKey($formDataNode['item']['breakpoint_field']) . '<to_timestamp(\'' . $breakpointEnd . '\', \'yyyy-mm-dd hh24:mi:ss\')';
            } else {
                $where = ' WHERE ';
                $where .= $db->quoteKey($formDataNode['item']['breakpoint_field']) . '>=' . $db->quoteValue($breakpointStart);
                $where .= ' AND ';
                $where .= $db->quoteKey($formDataNode['item']['breakpoint_field']) . '<' . $db->quoteValue($breakpointEnd);
            }
        }

        if ($formDataNode['item']['ds_type'] === 'table') {
            $sql = 'SELECT COUNT(*) FROM ' . $db->quoteKey($formDataNode['item']['ds_table']) . $where;
        } else {
            $sql = 'SELECT COUNT(*) FROM (' . $formDataNode['item']['ds_sql'] . ' ) t ' . $where;
        }

        $total = (int) $db->getValue($sql);
        if ($total === 0) {
            throw new ServiceException('节点 ' .$formDataNode['index'] . ' 未读取到符合条件的数据，无法完成数据流验证！');
        }

        if ($formDataNode['item']['ds_type'] === 'table') {
            $sql = 'SELECT * FROM ' . $db->quoteKey($formDataNode['item']['ds_table']) . $where . ' LIMIT 1';
        } else {
            $sql = 'SELECT * FROM (' . $formDataNode['item']['ds_sql']  . ' ) t ' . $where . ' LIMIT 1';
        }

        $output = $db->getObject($sql);
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
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_input_ds');
        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->ds_id = $formDataNode['item']['ds_id'];
        $tupleFlowNodeItem->ds_type = $formDataNode['item']['ds_type'];
        $tupleFlowNodeItem->ds_table = $formDataNode['item']['ds_table'];
        $tupleFlowNodeItem->ds_sql = $formDataNode['item']['ds_sql'];
        $tupleFlowNodeItem->breakpoint = $formDataNode['item']['breakpoint'];
        $tupleFlowNodeItem->breakpoint_field = $formDataNode['item']['breakpoint_field'];
        $tupleFlowNodeItem->breakpoint_time = $formDataNode['item']['breakpoint_time'];
        $tupleFlowNodeItem->breakpoint_step = $formDataNode['item']['breakpoint_step'];
        $tupleFlowNodeItem->breakpoint_offset = $formDataNode['item']['breakpoint_offset'];
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


    public function handle(object $input): array
    {


    }
}
