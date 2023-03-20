<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Input;


use Be\App\Etl\Service\Admin\FlowNode\Input;
use Be\Be;

class Csv extends Input
{


    public function test(array $formDataNode): object
    {

    }

    /**
     * 插入数据库
     * @param string $flowNodeId
     * @param array $formDataNode
     * @return object
     */
    public function insert(string $flowNodeId, array $formDataNode): object
    {

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
