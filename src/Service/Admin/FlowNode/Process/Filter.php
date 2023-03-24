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

        if (!isset($formDataNode['item']['filter_values']) || !is_string($formDataNode['item']['filter_values']) || $formDataNode['item']['filter_values'] === '') {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 过滤值列表（filter_values）参数无效！');
        }

        if (!isset($formDataNode['item']['op']) || !is_string($formDataNode['item']['op']) || !in_array($formDataNode['item']['op'], ['allow', 'deny'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 操作（op）参数无效！');
        }



        $matched = false;
        $filterValues = explode("\n", $formDataNode['item']['filter_values']);
        foreach ($filterValues as $filterValue) {
            $filterValue = trim($filterValue);
            if ($filterValue === '') continue;

            if (strpos($input->$filterField, $filterValue) !== false) {
                $matched = true;
                break;
            }
        }

        if ($matched) {
            if ($formDataNode['item']['op'] === 'allow') {
                $input->$filterField .= '（符合条件，放行）';
            } else {
                $input->$filterField .= '（符合条件，中止处理）';
            }
        } else {
            if ($formDataNode['item']['op'] === 'allow') {
                $input->$filterField .= '（不符合条件，中止处理）';
            } else {
                $input->$filterField .= '（不符合条件，放行）';
            }
        }

        return $input;
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
        $filterField = $flowNode->item->filter_field;

        $matched = false;
        foreach ($this->filterValues as $filterValue) {
            if (strpos($input->$filterField, $filterValue) !== false) {
                $matched = true;
                break;
            }
        }

        if ($matched) {
            if ($flowNode->item->op === 'allow') {
                return $input;
            } else {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 符合过滤条件，中止处理！');
            }
        } else {
            if ($flowNode->item->op === 'allow') {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 不符合过滤条件，中止处理！');
            } else {
                return $input;
            }
        }
    }


    public function finish(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $this->filterValues = null;
    }


}
