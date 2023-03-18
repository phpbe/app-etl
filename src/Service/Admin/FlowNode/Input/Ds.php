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
     * @param array $formData 表单数据
     * @return array 
     * @throws \Throwable
     */
    public function test(array $formData): object
    {
        if (!isset($formData['index']) || !is_numeric($formData['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formData['ds_id']) || !is_string($formData['ds_id']) || strlen($formData['ds_id']) !== 36) {
            throw new ServiceException('节点 ' .$formData['index'] . ' 数据源（ds_id）参数无效！');
        }

        if (!isset($formData['ds_type']) || !is_string($formData['ds_type']) || !in_array($formData['ds_type'], ['table', 'sql'])) {
            throw new ServiceException('节点 ' .$formData['index'] . ' 类型（ds_type）参数无效！');
        }

        if ($formData['ds_type'] === 'table') {
            if (!isset($formData['ds_table']) || !is_string($formData['ds_table']) || strlen($formData['ds_table']) === 0) {
                throw new ServiceException('节点 ' .$formData['index'] . ' 数据表（ds_table）参数无效！');
            }
            $formData['ds_sql'] = '';
        } else {
            if (!isset($formData['ds_sql']) || !is_string($formData['ds_sql']) || strlen($formData['ds_sql']) === 0) {
                throw new ServiceException('节点 ' .$formData['index'] . ' SQL（ds_sql）参数无效！');
            }
            $formData['ds_table'] = '';
        }

        if (!isset($formData['breakpoint']) || !is_string($formData['breakpoint']) || !in_array($formData['breakpoint'], ['full', 'breakpoint'])) {
            throw new ServiceException('节点 ' .$formData['index'] . ' 断点类型（breakpoint）参数无效！');
        }

        if ($formData['breakpoint'] === 'breakpoint') {

            if (!isset($formData['breakpoint_field']) || !is_string($formData['breakpoint_field']) || strlen($formData['breakpoint_field']) === 0) {
                throw new ServiceException('节点 ' .$formData['index'] . ' 断点字段（breakpoint_field）参数无效！');
            }

            if (!isset($formData['breakpoint_time']) || !is_string($formData['breakpoint_time']) || !strtotime($formData['breakpoint_time'])) {
                throw new ServiceException('节点 ' .$formData['index'] . ' 断点时间（breakpoint_time）参数无效！');
            }

            if (!isset($formData['breakpoint_step']) || !is_string($formData['breakpoint_step']) || !in_array($formData['breakpoint_step'], ['1_HOUR', '1_DAY', '1_MONTH'])) {
                throw new ServiceException('节点 ' .$formData['index'] . ' 断点递增量（breakpoint_step）参数无效！');
            }

            if (!isset($formData['breakpoint_offset']) || !is_numeric($formData['breakpoint_offset'])) {
                throw new ServiceException('节点 ' .$formData['index'] . ' 断点向前编移量（breakpoint_offset）无效！');
            }
            $formData['breakpoint_offset'] = (int) $formData['breakpoint_offset'];

        } else {
            $formData['breakpoint_field'] = '';
            $formData['breakpoint_time'] = '1970-01-02 00:00:00';
            $formData['breakpoint_step'] = '1_HOUR';
            $formData['breakpoint_offset'] = 0;
        }

        $db = Be::getService('App.Etl.Admin.Ds')->newDb($formData['ds_id']);

        $t = time();

        $breakpointEnd = null;
        $tBreakpointEnd = null;
        $where = '';
        if ($formData['breakpoint'] === 'breakpoint') { // 按断点同步
            $breakpointStart = $formData['breakpoint_time'];
            $tBreakpointStart = strtotime($breakpointStart);

            if ($tBreakpointStart > $t) {
                throw new ServiceException('节点 ' .$formData['index'] . ' 断点设置已超过当前时间，程序中止！');
            }

            switch ($formData['breakpoint_step']) {
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

            if ($formData['breakpoint_offset'] > 0) {
                $tBreakpointStart -= $formData['breakpoint_offset'];
                $breakpointStart = date('Y-m-d H:i:s', $tBreakpointStart);
            }

            if ($db instanceof \Be\Db\Driver\Oracle) {
                $where = ' WHERE ';
                $where .= $db->quoteKey($formData['breakpoint_field']) . '>=to_timestamp(\'' . $breakpointStart . '\', \'yyyy-mm-dd hh24:mi:ss\')';
                $where .= ' AND ';
                $where .= $db->quoteKey($formData['breakpoint_field']) . '<to_timestamp(\'' . $breakpointEnd . '\', \'yyyy-mm-dd hh24:mi:ss\')';
            } else {
                $where = ' WHERE ';
                $where .= $db->quoteKey($formData['breakpoint_field']) . '>=' . $db->quoteValue($breakpointStart);
                $where .= ' AND ';
                $where .= $db->quoteKey($formData['breakpoint_field']) . '<' . $db->quoteValue($breakpointEnd);
            }
        }

        if ($formData['ds_type'] === 'table') {
            $sql = 'SELECT COUNT(*) FROM ' . $db->quoteKey($formData['ds_table']) . $where;
        } else {
            $sql = 'SELECT COUNT(*) FROM (' . $formData['ds_sql'] . ' ) t ' . $where;
        }

        $total = (int) $db->getValue($sql);
        if ($total === 0) {
            throw new ServiceException('节点 ' .$formData['index'] . ' 未读取到符合条件的数据，无法完成数据流验证！');
        }

        if ($formData['ds_type'] === 'table') {
            $sql = 'SELECT * FROM ' . $db->quoteKey($formData['ds_table']) . $where . ' LIMIT 1';
        } else {
            $sql = 'SELECT * FROM (' . $formData['ds_sql']  . ' ) t ' . $where . ' LIMIT 1';
        }

        $output = $db->getObject($sql);
        return $output;
    }


    public function handle(object $input): array
    {


    }
}
