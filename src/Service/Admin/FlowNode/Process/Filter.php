<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Process;


use Be\App\Etl\Service\Admin\FlowNode\Process;
use Be\App\ServiceException;
use Be\Be;

class Filter extends Process
{

    public function getItemName(): string
    {
        return '过滤';
    }


    public function test(array $formDataNode, object $input): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['filter_field']) || !is_string($formDataNode['item']['filter_field']) || $formDataNode['item']['filter_field'] === '') {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 过滤字段（filter_field）参数无效！');
        }

        $filterField = $formDataNode['item']['filter_field'];
        if (!isset($input->$filterField)) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 过滤字段（'.$filterField.'）无效！');
        }

        if (!isset($formDataNode['item']['filter_op']) || !is_string($formDataNode['item']['filter_op']) || !in_array($formDataNode['item']['filter_op'], ['include', 'start', 'end', 'eq', 'gt', 'gte', 'lt', 'lte', 'between'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 过滤操作（filter_op）参数无效！');
        }

        if (!isset($formDataNode['item']['filter_values']) || !is_string($formDataNode['item']['filter_values']) || $formDataNode['item']['filter_values'] === '') {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 过滤值列表（filter_values）参数无效！');
        }

        if (!isset($formDataNode['item']['op']) || !is_string($formDataNode['item']['op']) || !in_array($formDataNode['item']['op'], ['allow', 'deny'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 操作（op）参数无效！');
        }

        $output = clone $input;

        $matched = false;
        $filterValues = explode("\n", $formDataNode['item']['filter_values']);
        foreach ($filterValues as $filterValue) {
            $filterValue = trim($filterValue);
            if ($filterValue === '') continue;

            switch ($formDataNode['item']['filter_op']) {
                case 'include':
                    if (strpos($output->$filterField, $filterValue) !== false) {
                        $matched = true;
                        break;
                    }
                    break;
                case 'start':
                    if (substr($output->$filterField, 0, strlen($filterValue)) === $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;
                case 'end':
                    if (substr($output->$filterField, -strlen($filterValue)) === $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;
                case 'eq':
                    if ($output->$filterField === $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;

                case 'gt':
                    if ($output->$filterField > $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;

                case 'gte':
                    if ($output->$filterField >= $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;

                case 'lt':
                    if ($output->$filterField < $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;

                case 'lte':
                    if ($output->$filterField <= $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;

                case 'between':
                    $arr = explode('|', $filterValue);
                    if (count($arr) === 2) {
                        if ($output->$filterField >= $arr[0] && $output->$filterField <= $arr[1]) {
                            $matched = true;
                            break;
                        }
                    }
                    break;
            }

        }

        if ($matched) {
            if ($formDataNode['item']['op'] === 'allow') {
                $output->$filterField .= '（符合条件，放行）';
            } else {
                $output->$filterField .= '（符合条件，中止处理）';
            }
        } else {
            if ($formDataNode['item']['op'] === 'allow') {
                $output->$filterField .= '（不符合条件，中止处理）';
            } else {
                $output->$filterField .= '（不符合条件，放行）';
            }
        }

        return $output;
    }


    public function edit(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_process_filter');

        if (isset($formDataNode['item']['id']) && is_string($formDataNode['item']['id']) && strlen($formDataNode['item']['id']) === 36) {
            try {
                $tupleFlowNodeItem->load($formDataNode['item']['id']);
            } catch (\Throwable $t) {
            }
        }

        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->filter_field = $formDataNode['item']['filter_field'];
        $tupleFlowNodeItem->filter_op = $formDataNode['item']['filter_op'];
        $tupleFlowNodeItem->filter_values = $formDataNode['item']['filter_values'];
        $tupleFlowNodeItem->op = $formDataNode['item']['op'];
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


    private ?array $filterValues = null;

    public function start(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $newFilterValues = [];
        $filterValues = explode("\n", $flowNode->item->filter_values);
        foreach ($filterValues as $filterValue) {
            $filterValue = trim($filterValue);
            if ($filterValue === '') continue;

            $newFilterValues[] = $filterValue;
        }

        $this->filterValues = $newFilterValues;
    }


    public function process(object $flowNode, object $input, object $flowLog, object $flowNodeLog): object
    {
        $output = clone $input;

        $filterField = $flowNode->item->filter_field;

        $matched = false;
        foreach ($this->filterValues as $filterValue) {

            switch ($flowNode->item->filter_op) {
                case 'include':
                    if (strpos($output->$filterField, $filterValue) !== false) {
                        $matched = true;
                        break;
                    }
                    break;
                case 'start':
                    if (substr($output->$filterField, 0, strlen($filterValue)) === $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;
                case 'end':
                    if (substr($output->$filterField, -strlen($filterValue)) === $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;
                case 'eq':
                    if ($output->$filterField === $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;

                case 'gt':
                    if ($output->$filterField > $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;

                case 'gte':
                    if ($output->$filterField >= $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;

                case 'lt':
                    if ($output->$filterField < $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;

                case 'lte':
                    if ($output->$filterField <= $filterValue) {
                        $matched = true;
                        break;
                    }
                    break;

                case 'between':
                    $arr = explode('|', $filterValue);
                    if (count($arr) === 2) {
                        if ($output->$filterField >= $arr[0] && $output->$filterField <= $arr[1]) {
                            $matched = true;
                            break;
                        }
                    }
                    break;
            }

        }

        if ($matched) {
            if ($flowNode->item->op === 'allow') {
                return $output;
            } else {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 符合过滤条件，中止处理！');
            }
        } else {
            if ($flowNode->item->op === 'allow') {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 不符合过滤条件，中止处理！');
            } else {
                return $output;
            }
        }
    }


    public function finish(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $this->filterValues = null;
    }


}
