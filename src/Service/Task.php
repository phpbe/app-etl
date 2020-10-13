<?php

namespace Be\App\Etl\Service;


use Be\System\Be;
use Be\System\Db\Tuple;
use Be\System\Exception\ServiceException;
use Be\System\Exception\TupleException;
use Be\Util\Datetime;

class Task extends \Be\System\Service
{

    /**
     * 抽取任务
     *
     * @param int $extractId 抽取任务ID
     * @param int $timestamp 当前时间点
     * @param int $trigger 触发方式：0：系统调度/1：人工启动
     */
    public function runExtract($extractId, $timestamp, $trigger = 0)
    {
        $db = Be::getDb();
        $extractLog = null;
        try {
            $extract = Be::newTuple('etl_extract')->load($extractId);

            if ($trigger == 0) {
                // 系统自动运行时，校验时间是否匹配计划设置
                if (!$this->isOnTime($extract->schedule, $timestamp)) {
                    return;
                }
            }

            $configExtract = Be::getConfig('Etl.Extract');

            $runningExtractLog = Be::newTuple('etl_extract_log');
            try {
                $runningExtractLog->loadBy([
                    'extract_id' => $extractId,
                    'status' => 1,
                ]);

                if (time() - strtotime($runningExtractLog->update_time) > $configExtract->timeout) {
                    $runningExtractLog->status = -1;
                    $runningExtractLog->message = '执行超过1小时未更新';
                    $runningExtractLog->update();
                } else {
                    // 抽取任务仍在运行中...
                    return;
                }
            } catch (\Exception $e) {
            }

            $extractLog = Be::newTuple('etl_extract_log');
            $extractLog->extract_id = $extract->id;
            $extractLog->breakpoint = $extract->breakpoint; // 断点
            $extractLog->breakpoint_step = $extract->breakpoint_step; // 断点递增量
            $extractLog->total = 0; // 总数据量
            $extractLog->offset = 0; // 已处理数据量
            $extractLog->status = 1; // 状态（0：创建/1：运行中/2：执行完成/-1：出错）
            $extractLog->message = ''; // 异常信息
            $extractLog->trigger = $trigger;
            $extractLog->complete_time = null;
            $extractLog->create_time = date('Y-m-d H:i:s');
            $extractLog->update_time = date('Y-m-d H:i:s');
            $extractLog->save();

            $srcDs = Be::newTuple('etl_ds')->load($extract->src_ds_id);
            $dstDs = Be::newTuple('etl_ds')->load($extract->dst_ds_id);

            $extractSnapshot = Be::newTuple('etl_extract_snapshot');
            $extractSnapshot->extract_log_id = $extractLog->id;
            $extractSnapshot->extract_id = $extract->id;
            $extractSnapshot->extract_data = json_encode($extract->toArray());
            $extractSnapshot->src_ds_id = $extract->src_ds_id;
            $extractSnapshot->src_ds_data = json_encode($srcDs->toArray());
            $extractSnapshot->dst_ds_id = $extract->dst_ds_id;
            $extractSnapshot->dst_ds_data = json_encode($dstDs->toArray());
            $extractSnapshot->create_time = date('Y-m-d H:i:s');
            $extractSnapshot->save();

            $dbSrc = Be::getService('Etl.Ds')->getDb($extract->src_ds_id);
            $dbDst = Be::getService('Etl.Ds')->getDb($extract->dst_ds_id);

            $breakpointEnd = null;
            $tBreakpointEnd = null;
            $where = '';
            if ($extract->breakpoint_type == '1') { // 按断点同步
                $breakpointStart = $extract->breakpoint;
                $tBreakpointStart = strtotime($breakpointStart);
                if ($extract->breakpoint_offset > 0) {
                    $tBreakpointStart -= $extract->breakpoint_offset;
                    $breakpointStart = date('Y-m-d H:i:s', $tBreakpointStart);
                }

                if ($tBreakpointStart > $timestamp) {
                    throw new ServiceException('断点设置已超过当前时间，程序中止！');
                }

                switch ($extract->breakpoint_step) {
                    case '1_HOUR':
                        $tBreakpointEnd = $tBreakpointStart + 3600;
                        if ($tBreakpointEnd > $timestamp) {
                            $tBreakpointEnd = $timestamp;
                        }
                        $breakpointEnd = date('Y-m-d H:i:s', $tBreakpointEnd);
                        break;
                    case '1_DAY':
                        $tBreakpointEnd = $tBreakpointStart + 86400;
                        if ($tBreakpointEnd > $timestamp) {
                            $tBreakpointEnd = $timestamp;
                        }
                        $breakpointEnd = date('Y-m-d H:i:s', $tBreakpointEnd);
                        break;
                    case '1_MONTH':
                        $breakpointEnd = Datetime::getNextMonth($breakpointStart);
                        $tBreakpointEnd = strtotime($breakpointEnd);
                        if ($tBreakpointEnd > $timestamp) {
                            $tBreakpointEnd = $timestamp;
                            $breakpointEnd = date('Y-m-d H:i:s', $tBreakpointEnd);
                        }
                        break;
                    default:
                        throw new ServiceException('断点递增量(' . $extract->breakpoint_step . ')无法识别！');
                }

                $where = ' WHERE ';
                $where .= $dbSrc->quoteKey($extract->breakpoint_field) . '>=' . $dbSrc->quoteValue($breakpointStart);
                $where .= ' AND ';
                $where .= $dbSrc->quoteKey($extract->breakpoint_field) . '<' . $dbSrc->quoteValue($breakpointEnd);
            }

            $sql = 'SELECT COUNT(*) FROM ' . $dbSrc->quoteKey($extract->src_table) . $where;
            $total = $dbSrc->getValue($sql);
            $extractLog->total = $total;
            $extractLog->update_time = date('Y-m-d H:i:s');
            $extractLog->save();

            $fieldMappingKv = null;
            $fieldMappingFn = null;
            switch ($extract->field_mapping_type) {
                case '0':

                    break;
                case '1':
                    $fieldMappings = explode(',', $extract->field_mapping);
                    foreach ($fieldMappings as $fieldMapping) {
                        $fieldMapping = trim($fieldMapping);
                        if (!$fieldMapping) {
                            continue;
                        }

                        $fieldMapping = explode(':', $fieldMapping);
                        if (count($fieldMapping) == 2) {
                            $fieldMappingKv[trim($fieldMapping[0])] = $fieldMapping[1];
                        }
                    }
                    break;
                case '2':
                    $fieldMappingFn = eval('return function($row){' . $extract->field_mapping_code . '};');
                    break;
                default:
                    throw new ServiceException('字段映射类型(' . $extract->field_mapping_type . ')无法识别！');
            }

            // 全量同步时先删数据
            if ($extract->breakpoint_type == '0') {
                $sql = null;
                switch ($configExtract->clearType) {
                    case 'truncate':
                        $sql = 'TRUNCATE TABLE ' . $dbSrc->quoteKey($extract->dst_table);
                        break;
                    case 'delete':
                        $sql = 'DELETE FROM ' . $dbSrc->quoteKey($extract->dst_table);
                        break;
                }
                $dbDst->query($sql);
            }

            $sql = null;
            if ($extract->src_type == '0') {
                $sql = 'SELECT * FROM ' . $dbSrc->quoteKey($extract->src_table) . $where;
            } else {
                $sql = 'SELECT * FROM (' . $extract->src_sql . ' ) t ' . $where;
            }

            $srcRows = $dbSrc->getYieldArrays($sql);

            $dbDstDriverName = $dbDst->getDriverName();

            // 全量插入
            if ($extract->breakpoint_type == '0') {

                $batchData = [];
                $offset = 0;
                foreach ($srcRows as $srcRow) {
                    $extractLog->offset++;
                    $offset++;

                    $dstRow = null;
                    switch ($extract->field_mapping_type) {
                        case '0':
                            $dstRow = $srcRow;
                            break;
                        case '1':
                            foreach ($fieldMappingKv as $k => $v) {
                                $dstRow[$v] = $srcRow[$k];
                            }
                            break;
                        case '2':
                            $dstRow = $fieldMappingFn($srcRow);
                            break;
                    }

                    $batchData[] = $dstRow;

                    if ($offset >= $configExtract->batchQuantity) {
                        $offset = 0;
                        // mysql 支持 replace into， 特殊处理
                        if ($dbDstDriverName == 'Mysql') {
                            $dbDst->quickReplaceMany($extract->dst_table, $batchData);
                        } else {
                            $dbDst->quickInsertMany($extract->dst_table, $batchData);
                        }
                        $batchData = [];

                        $extractLog->update_time = date('Y-m-d H:i:s');
                        $extractLog->save();
                    }
                }

                if (count($batchData) > 0) {
                    // mysql 支持 replace into， 特殊处理
                    if ($dbDstDriverName == 'Mysql') {
                        $dbDst->quickReplaceMany($extract->dst_table, $batchData);
                    } else {
                        $dbDst->quickInsertMany($extract->dst_table, $batchData);
                    }
                    $batchData = [];
                }

            } else { // 境量方式

                // mysql 支持 replace into， 特殊处理
                if ($dbDstDriverName == 'Mysql') {
                    $batchData = [];
                    $offset = 0;
                    foreach ($srcRows as $srcRow) {
                        $extractLog->offset++;
                        $offset++;

                        $dstRow = null;
                        switch ($extract->field_mapping_type) {
                            case '0':
                                $dstRow = $srcRow;
                                break;
                            case '1':
                                foreach ($fieldMappingKv as $k => $v) {
                                    $dstRow[$v] = $srcRow[$k];
                                }
                                break;
                            case '2':
                                $dstRow = $fieldMappingFn($srcRow);
                                break;
                        }

                        $batchData[] = $dstRow;

                        if ($offset >= $configExtract->batchQuantity) {
                            $offset = 0;

                            $dbDst->quickReplaceMany($extract->dst_table, $batchData);
                            $batchData = [];

                            $extractLog->update_time = date('Y-m-d H:i:s');
                            $extractLog->save();
                        }
                    }

                    if (count($batchData) > 0) {
                        $dbDst->quickReplaceMany($extract->dst_table, $batchData);
                        $batchData = [];
                    }
                } else {

                    $primaryKey = $dbDst->getTablePrimaryKey($extract->dst_table);
                    $primaryKeyFields = null;
                    if (is_array($primaryKey)) { // 多主键
                        $primaryKeyFields = 'CONCAT(';
                        foreach ($primaryKey as $pKey) {
                            $primaryKeyFields .= $dbDst->quoteKey($pKey) . ', \',\',';
                        }
                        $primaryKeyFields = substr($primaryKeyFields, -1);
                        $primaryKeyFields .=  ')';
                    }

                    $batchData = [];
                    $primaryKeyIn = [];
                    $offset = 0;
                    foreach ($srcRows as $srcRow) {
                        $extractLog->offset++;
                        $offset++;

                        $dstRow = null;
                        switch ($extract->field_mapping_type) {
                            case '0':
                                $dstRow = $srcRow;
                                break;
                            case '1':
                                foreach ($fieldMappingKv as $k => $v) {
                                    $dstRow[$v] = $srcRow[$k];
                                }
                                break;
                            case '2':
                                $dstRow = $fieldMappingFn($srcRow);
                                break;
                        }

                        $batchData[] = $dstRow;

                        if (is_array($primaryKey)) { // 多主键
                            $pKeyIn = [];
                            foreach ($primaryKey as $pKey) {
                                $pKeyIn[] = $dbDst->quoteValue($dstRow[$pKey]);
                            }
                            $primaryKeyIn[] = '(' . implode(',', $pKeyIn) . ')';
                        } elseif ($primaryKey) {
                            $primaryKeyIn[] = $dstRow[$primaryKey];
                        }

                        if ($offset >= $configExtract->batchQuantity) {
                            $offset = 0;

                            $batchInsertData = [];
                            $batchUpdateData = [];
                            if (is_array($primaryKey)) { // 多主键
                                $sql = 'SELECT ' . $primaryKeyFields . ' 
                                        FROM ' . $dbSrc->quoteKey($extract->dst_table) . ' 
                                        WHERE (' . $primaryKeyFields . ') IN (' . implode(',', $primaryKeyIn) . ')';
                                $exists = $dbDst->getValues($sql);
                                if (count($exists) == 0) {
                                    $batchInsertData = $batchData;
                                } else {
                                    foreach ($batchData as $row) {
                                        $key = '';
                                        foreach ($primaryKey as $pKey) {
                                            $key .= $row[$pKey] . ',';
                                        }
                                        if (in_array($key, $exists)) {
                                            $batchUpdateData[] = $row;
                                        } else {
                                            $batchInsertData[] = $row;
                                        }
                                    }
                                }
                            } elseif ($primaryKey) {
                                $sql = 'SELECT ' . $primaryKey . ' 
                                        FROM ' . $dbSrc->quoteKey($extract->dst_table) . ' 
                                        WHERE ' . $primaryKey . ' IN (' . implode(',', $primaryKeyIn) . ')';
                                $exists = $dbDst->getValues($sql);

                                if (count($exists) == 0) {
                                    $batchInsertData = $batchData;
                                } else {
                                    foreach ($batchData as $row) {
                                        // 已存在的更新，不存在的插入
                                        if (in_array($row[$primaryKey], $exists)) {
                                            $batchUpdateData[] = $row;
                                        } else {
                                            $batchInsertData[] = $row;
                                        }
                                    }
                                }
                            } else {
                                $batchInsertData[] = $batchData;
                            }

                            if (count($batchInsertData) > 0) {
                                $dbDst->quickInsertMany($extract->dst_table, $batchInsertData);
                                $batchInsertData = [];
                            }

                            if (count($batchUpdateData) > 0) {
                                $dbDst->quickUpdateMany($extract->dst_table, $batchUpdateData);
                                $batchUpdateData = [];
                            }

                            $batchData = [];
                            $primaryKeyIn = [];

                            $extractLog->update_time = date('Y-m-d H:i:s');
                            $extractLog->save();
                        }
                    }

                    if (count($batchData) > 0 && count($primaryKeyIn) > 0) {
                        $batchInsertData = [];
                        $batchUpdateData = [];
                        if (is_array($primaryKey)) { // 多主键
                            $sql = 'SELECT ' . $primaryKeyFields . ' 
                                    FROM ' . $dbSrc->quoteKey($extract->dst_table) . ' 
                                    WHERE (' . $primaryKeyFields . ') IN (' . implode(',', $primaryKeyIn) . ')';
                            $exists = $dbDst->getValues($sql);
                            if (count($exists) == 0) {
                                $batchInsertData = $batchData;
                            } else {
                                foreach ($batchData as $row) {
                                    $key = '';
                                    foreach ($primaryKey as $pKey) {
                                        $key .= $row[$pKey] . ',';
                                    }
                                    if (in_array($key, $exists)) {
                                        $batchUpdateData[] = $row;
                                    } else {
                                        $batchInsertData[] = $row;
                                    }
                                }
                            }
                        } elseif ($primaryKey) {
                            $sql = 'SELECT ' . $primaryKey . ' 
                                    FROM ' . $dbSrc->quoteKey($extract->dst_table) . ' 
                                    WHERE ' . $primaryKey . ' IN (' . implode(',', $primaryKeyIn) . ')';
                            $exists = $dbDst->getValues($sql);

                            if (count($exists) == 0) {
                                $batchInsertData = $batchData;
                            } else {
                                foreach ($batchData as $row) {
                                    // 已存在的更新，不存在的插入
                                    if (in_array($row[$primaryKey], $exists)) {
                                        $batchUpdateData[] = $row;
                                    } else {
                                        $batchInsertData[] = $row;
                                    }
                                }
                            }
                        } else {
                            $batchInsertData[] = $batchData;
                        }

                        if (count($batchInsertData) > 0) {
                            $dbDst->quickInsertMany($extract->dst_table, $batchInsertData);
                            $batchInsertData = [];
                        }

                        if (count($batchUpdateData) > 0) {
                            $dbDst->quickUpdateMany($extract->dst_table, $batchUpdateData);
                            $batchUpdateData = [];
                        }

                        $batchData = [];
                        $primaryKeyIn = [];
                    }
                }
            }

            $extractLog->status = 2;
            $extractLog->complete_time = date('Y-m-d H:i:s');
            $extractLog->update_time = date('Y-m-d H:i:s');
            $extractLog->save();

            if ($extract->breakpoint_type == '1') {
                // 按断点同步时，更新断点
                $db->update('etl_extract', [
                    'id' => $extractId,
                    'breakpoint' => $breakpointEnd,
                ]);
            }

        } catch (\Throwable $e) {
            echo $e->getMessage();
            print_r($e->getTrace());

            if ($extractLog !== null) {

                $db = Be::newDb();

                $db->update('etl_extract_log', [
                    'id' => $extractLog->id,
                    'status' => -1,
                    'message' => $e->getMessage(),
                    'update_time' => date('Y-m-d H:i:s'),
                ], 'id');

                $db->insert('etl_extract_exception', [
                    'extract_log_id' => $extractLog->id,
                    'extract_id' => $extractLog->extract_id,
                    'message' => $e->getMessage(),
                    'trace' => print_r($e->getTrace(), true),
                    'create_time' => date('Y-m-d H:i:s'),
                ]);
            }

            $config = Be::getConfig('Etl.Notify');
            $serviceNotify = Be::getService('Etl.Notify');
            if ($config->mail) {
                $serviceNotify->mail('抽取数据任务发生异常：' . $e->getMessage());
            }

            if ($config->dingTalkRobot) {
                $serviceNotify->dingTalkRobot('抽取数据任务发生异常：' . $e->getMessage());
            }
        }
    }

    /**
     * 加工任务
     *
     * @param int $extractId 加工任务ID
     * @param int $timestamp 当前时间点
     * @param int $trigger 触发方式：0：系统调度/1：人工启动
     */
    public function runTransform($extractId, $timestamp, $trigger = 0)
    {
        try {
            $transform = Be::newTuple('etl_extract')->load($extractId);

            $dbSrc = Be::getService('Etl.Ds')->getDb($transform->src_ds_id);
            $dbDst = Be::getService('Etl.Ds')->getDb($transform->dst_ds_id);


        } catch (\Throwable $e) {
            // 通知
        }
    }


    /**
     * 执行计划是否匹配对应时间
     *
     * @param string $schedule 执行计划，如: 0-29/2,30-59/3 1-2,4 1,3,5,7,9 1-6 *
     * @param int $timestamp 指定时间戳
     * @return bool
     */
    public function isOnTime($schedule, $timestamp = 0)
    {
        $schedule = explode(' ', $schedule);
        if (count($schedule) != 5) return false;

        if ($timestamp == 0) $timestamp = time();

        return $this->isScheduleMatch($schedule[0], date('i', $timestamp)) &&
            $this->isScheduleMatch($schedule[1], date('G', $timestamp)) &&
            $this->isScheduleMatch($schedule[2], date('j', $timestamp)) &&
            $this->isScheduleMatch($schedule[3], date('n', $timestamp)) &&
            $this->isScheduleMatch($schedule[4], date('N', $timestamp));
    }

    /**
     * 比对计划任务时间配置项是否匹配当前时间
     *
     * @param string $scheduleRule 计划任务时间配置项规则
     * @param int $timeValue 时间值
     * @return bool
     */
    protected function isScheduleMatch($scheduleRule, $timeValue)
    {

        if (!is_numeric($timeValue)) return false;
        $timeValue = intval($timeValue);

        $match = false;
        if ($scheduleRule == '*') {
            $match = true;
        } else {
            $scheduleRules = explode(',', $scheduleRule);
            foreach ($scheduleRules as $scheduleRule) {
                // 0-29/3
                if (strpos($scheduleRule, '/')) {
                    $fraction = explode('/', $scheduleRule);
                    if (count($fraction) != 2) {
                        continue;
                    }

                    $numerator = $fraction[0];
                    $denominator = $fraction[1];

                    if (!is_numeric($denominator)) {
                        continue;
                    }

                    if (strpos($numerator, '-')) {

                        $scheduleRuleValues = explode('-', $numerator);
                        if (count($scheduleRuleValues) != 2) {
                            continue;
                        }

                        if (!is_numeric($scheduleRuleValues[0]) || !is_numeric($scheduleRuleValues[1])) {
                            continue;
                        }

                        if ($scheduleRuleValues[0] <= $timeValue && $timeValue <= $scheduleRuleValues[1]) {
                            if (($timeValue - $scheduleRuleValues[0]) % $denominator == 0) {
                                $match = true;
                                break;
                            }
                        }

                    } else {
                        if ($numerator == '*') {
                            if ($timeValue % $denominator == 0) {
                                $match = true;
                                break;
                            }
                        }
                    }
                } else {
                    // 30-59
                    if (strpos($scheduleRule, '-')) {
                        $scheduleRuleValues = explode('-', $scheduleRule);
                        if (count($scheduleRuleValues) != 2) {
                            continue;
                        }

                        if (!is_numeric($scheduleRuleValues[0]) || !is_numeric($scheduleRuleValues[1])) {
                            continue;
                        }

                        if ($scheduleRuleValues[0] <= $timeValue && $timeValue <= $scheduleRuleValues[1]) {
                            $match = true;
                            break;
                        }
                    } else {
                        if ($scheduleRule == '*' || $scheduleRule == $timeValue) {
                            $match = true;
                            break;
                        }
                    }
                }
            }
        }

        return $match;
    }


}
