<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Process;


use Be\App\Etl\Service\Admin\FlowNode\Process;
use Be\App\ServiceException;
use Be\Be;

class Clean extends Process
{

    public function getItemName(): string
    {
        return '清洗';
    }


    public function test(array $formDataNode, object $input): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['clean_field']) || !is_string($formDataNode['item']['clean_field']) || $formDataNode['item']['clean_field'] === '') {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 清洗字段（clean_field）参数无效！');
        }

        $cleanField = $formDataNode['item']['clean_field'];
        if (!isset($input->$cleanField)) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 清洗字段（'.$cleanField.'）无效！');
        }

        if (!isset($formDataNode['item']['clean_values']) || !is_string($formDataNode['item']['clean_values']) || $formDataNode['item']['clean_values'] === '') {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 清洗掉的内容列表（clean_values）参数无效！');
        }


        if (!isset($formDataNode['item']['insert_tags']) || !is_numeric($formDataNode['item']['insert_tags'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 插入标签（insert_tags）参数无效！');
        }

        $formDataNode['item']['insert_tags'] = (int)$formDataNode['item']['insert_tags'];

        if (!in_array($formDataNode['item']['insert_tags'], [0, 1])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 插入标签（insert_tags）参数无效！');
        }

        if ($formDataNode['item']['insert_tags'] === 1) {
            foreach (get_object_vars($input) as $k => $v) {
                $formDataNode['item']['clean_values'] = str_replace('{' . $k . '}', $v, $formDataNode['item']['clean_values']);
            }
        }


        if (!isset($formDataNode['item']['match_case']) || !is_numeric($formDataNode['item']['match_case'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 标记清洗过（match_case）参数无效！');
        }

        $formDataNode['item']['match_case'] = (int)$formDataNode['item']['match_case'];

        if (!in_array($formDataNode['item']['match_case'], [0, 1])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 区分大小写（match_case）参数无效！');
        }


        $output = clone $input;

        $matched = false;
        $cleanValues = explode("\n", $formDataNode['item']['clean_values']);
        foreach ($cleanValues as $cleanValue) {
            //$cleanValue = trim($cleanValue);
            if ($cleanValue === '') continue;

            if ($formDataNode['item']['insert_tags'] === 1) {
                $cleanValue = str_replace('{换行符}', "\n", $cleanValue);
            }

            $arr = explode('|||', $cleanValue);
            if (count($arr) === 2) {
                if ($formDataNode['item']['match_case'] === 0) {
                    if (stripos($output->$cleanField, $arr[0]) !== false) {
                        $output->$cleanField = str_ireplace($arr[0], $arr[1], $output->$cleanField);
                        $matched = true;
                    }
                } else {
                    if (strpos($output->$cleanField, $arr[0]) !== false) {
                        $output->$cleanField = str_replace($arr[0], $arr[1], $output->$cleanField);
                        $matched = true;
                    }
                }
            } else {
                if ($formDataNode['item']['match_case'] === 0) {
                    if (stripos($output->$cleanField, $cleanValue) !== false) {
                        $output->$cleanField = str_ireplace($cleanValue, '', $output->$cleanField);
                        $matched = true;
                    }
                } else {
                    if (strpos($output->$cleanField, $cleanValue) !== false) {
                        $output->$cleanField = str_replace($cleanValue, '', $output->$cleanField);
                        $matched = true;
                    }
                }
            }
        }

        if (!isset($formDataNode['item']['sign']) || !is_numeric($formDataNode['item']['sign'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 标记清洗过（sign）参数无效！');
        }

        $formDataNode['item']['sign'] = (int)$formDataNode['item']['sign'];

        if (!in_array($formDataNode['item']['sign'], [0, 1])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 标记清洗过（sign）参数无效！');
        }

        if ($formDataNode['item']['sign'] === 1) {
            if (!isset($formDataNode['item']['sign_field']) || !is_string($formDataNode['item']['sign_field']) || $formDataNode['item']['sign_field'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 标记字段名（sign_field）参数无效！');
            }

            if (!isset($formDataNode['item']['sign_field_value_0']) || !is_string($formDataNode['item']['sign_field_value_0']) || $formDataNode['item']['sign_field_value_0'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 标记字段值（默认值）（sign_field_value_0）参数无效！');
            }

            if (!isset($formDataNode['item']['sign_field_value_1']) || !is_string($formDataNode['item']['sign_field_value_1']) || $formDataNode['item']['sign_field_value_1'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 标记字段值（已清洗）（sign_field_value_1）参数无效！');
            }

            $signField = $formDataNode['item']['sign_field'];
            if ($matched) {
                $output->$signField = $formDataNode['item']['sign_field_value_1'];
            } else {
                $output->$signField = $formDataNode['item']['sign_field_value_0'];
            }
        } else {
            $formDataNode['item']['sign_field'] = '';
            $formDataNode['item']['sign_field_value_0'] = '';
            $formDataNode['item']['sign_field_value_1'] = '';
        }

        return $output;
    }


    public function edit(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_process_clean');

        if (isset($formDataNode['item']['id']) && is_string($formDataNode['item']['id']) && strlen($formDataNode['item']['id']) === 36) {
            try {
                $tupleFlowNodeItem->load($formDataNode['item']['id']);
            } catch (\Throwable $t) {
            }
        }

        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->clean_field = $formDataNode['item']['clean_field'];
        $tupleFlowNodeItem->clean_values = $formDataNode['item']['clean_values'];
        $tupleFlowNodeItem->insert_tags = $formDataNode['item']['insert_tags'];
        $tupleFlowNodeItem->match_case = $formDataNode['item']['match_case'];
        $tupleFlowNodeItem->sign = $formDataNode['item']['sign'];
        $tupleFlowNodeItem->sign_field = $formDataNode['item']['sign_field'];
        $tupleFlowNodeItem->sign_field_value_0 = $formDataNode['item']['sign_field_value_0'];
        $tupleFlowNodeItem->sign_field_value_1 = $formDataNode['item']['sign_field_value_1'];
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


    private ?array $cleanValues = null;

    public function start(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $flowNode->item->insert_tags = (int)$flowNode->item->insert_tags;
        $flowNode->item->match_case = (int)$flowNode->item->match_case;
        $flowNode->item->sign = (int)$flowNode->item->sign;

        if ($flowNode->item->insert_tags === 0) {
            $newCleanValues = [];
            $cleanValues = explode("\n", $flowNode->item->clean_values);
            foreach ($cleanValues as $cleanValue) {
                $cleanValue = trim($cleanValue);
                if ($cleanValue === '') continue;

                $newCleanValues[] = $cleanValue;
            }

            $this->cleanValues = $newCleanValues;
        }
    }


    public function process(object $flowNode, object $input, object $flowLog, object $flowNodeLog): object
    {
        $output = clone $input;

        $cleanField = $flowNode->item->clean_field;

        if ($flowNode->item->insert_tags === 1) {
            $cleanValues = $flowNode->item->clean_values;
            foreach (get_object_vars($input) as $k => $v) {
                $cleanValues = str_replace('{' . $k . '}', $v, $cleanValues);
            }

            $newCleanValues = [];
            $cleanValues = explode("\n", $cleanValues);
            foreach ($cleanValues as $cleanValue) {
                $cleanValue = trim($cleanValue);
                if ($cleanValue === '') continue;

                $newCleanValues[] = $cleanValue;
            }

            $cleanValues = $newCleanValues;
        } else {
            $cleanValues = $this->cleanValues;
        }

        $matched = false;
        foreach ($cleanValues as $cleanValue) {
            //$cleanValue = trim($cleanValue);
            if ($cleanValue === '') continue;

            if ($flowNode->item->insert_tags === 1) {
                $cleanValue = str_replace('{换行符}', "\n", $cleanValue);
            }

            $arr = explode('|||', $cleanValue);

            if (count($arr) === 2) {
                if ($flowNode->item->match_case === 0) {
                    if (stripos($output->$cleanField, $arr[0]) !== false) {
                        $output->$cleanField = str_ireplace($arr[0], $arr[1], $output->$cleanField);
                        $matched = true;
                    }
                } else {
                    if (strpos($output->$cleanField, $arr[0]) !== false) {
                        $output->$cleanField = str_replace($arr[0], $arr[1], $output->$cleanField);
                        $matched = true;
                    }
                }
            } else {
                if ($flowNode->item->match_case === 0) {
                    if (stripos($output->$cleanField, $cleanValue) !== false) {
                        $output->$cleanField = str_ireplace($cleanValue, '', $output->$cleanField);
                        $matched = true;
                    }
                } else {
                    if (strpos($output->$cleanField, $cleanValue) !== false) {
                        $output->$cleanField = str_replace($cleanValue, '', $output->$cleanField);
                        $matched = true;
                    }
                }
            }
        }

        if ($flowNode->item->sign === 1) {
            $signField = $flowNode->item->sign_field;
            if ($matched) {
                $output->$signField = $flowNode->item->sign_field_value_1;
            } else {
                $output->$signField = $flowNode->item->sign_field_value_0;
            }
        }

        return $output;

    }

}
