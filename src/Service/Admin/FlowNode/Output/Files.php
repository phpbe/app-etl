<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Output;


use Be\App\Etl\Service\Admin\FlowNode\Output;
use Be\App\ServiceException;
use Be\Be;

class Files extends Output
{

    public function getItemName(): string
    {
        return '文件包';
    }

    public function test(array $formDataNode, object $input): object
    {

        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['name']) || !is_string($formDataNode['item']['name']) || !in_array($formDataNode['item']['name'], ['template', 'code'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 文件名处理方式（name）参数无效！');
        }

        $output = new \stdClass();

        if ($formDataNode['item']['name'] === 'template') {

            if (!isset($formDataNode['item']['name_template']) || !is_string($formDataNode['item']['name_template']) || $formDataNode['item']['name_template'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 文件名称模板（name_template）参数无效！');
            }

            $name = $formDataNode['item']['name_template'];
            foreach ((array)$input as $k => $v) {
                $name = str_replace('{' . $k . '}', $v, $name);
            }
            $output->name = $name;

        } else {

            if (!isset($formDataNode['item']['name_code']) || !is_string($formDataNode['item']['name_code']) || $formDataNode['item']['name_code'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 文件名代码处理（name_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): string {' . $formDataNode['item']['name_code'] . '};');
                $output->name = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 文件名代码处理（name_code）执行出错：' . $t->getMessage());
            }
        }

        if (!isset($formDataNode['item']['content']) || !is_string($formDataNode['item']['content']) || !in_array($formDataNode['item']['content'], ['template', 'code'])) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 内容处理方式（content）参数无效！');
        }

        if ($formDataNode['item']['content'] === 'template') {

            if (!isset($formDataNode['item']['content_template']) || !is_string($formDataNode['item']['content_template']) || $formDataNode['item']['content_template'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 文件内容模板（content_template）参数无效！');
            }

            $content = $formDataNode['item']['content_template'];
            foreach ((array)$input as $k => $v) {
                $content = str_replace('{' . $k . '}', $v, $content);
            }

            $len = mb_strlen($content);
            if ($len > 100) {
                $testContent = '（内容长度：' . $len . '）' . mb_substr($content, 0, 100) . '...';
            } else {
                $testContent = $content;
            }

            $output->content = $testContent;

        } else {

            if (!isset($formDataNode['item']['content_code']) || !is_string($formDataNode['item']['content_code']) || $formDataNode['item']['content_code'] === '') {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 文件内容代码处理（content_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): string {' . $formDataNode['item']['content_code'] . '};');

                $content = $fn($input);
                $output->content = '内容长度：' . mb_strlen($content);

            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 文件内容代码处理（content_code）执行出错：' . $t->getMessage());
            }
        }

        if (count((array)$output) === 0) {
            throw new ServiceException('节点 ' . ($formDataNode['index'] + 1) . ' 输出数据为空！');
        }

        return $output;
    }


    public function edit(string $flowNodeId, array $formDataNode): object
    {
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_output_files');

        if (isset($formDataNode['item']['id']) && is_string($formDataNode['item']['id']) && strlen($formDataNode['item']['id']) === 36) {
            try {
                $tupleFlowNodeItem->load($formDataNode['item']['id']);
            } catch (\Throwable $t) {
            }
        }

        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->name = $formDataNode['item']['name'];
        $tupleFlowNodeItem->name_template = $formDataNode['item']['name_template'];
        $tupleFlowNodeItem->name_code = $formDataNode['item']['name_code'];
        $tupleFlowNodeItem->content = $formDataNode['item']['content'];
        $tupleFlowNodeItem->content_template = $formDataNode['item']['content_template'];
        $tupleFlowNodeItem->content_code = $formDataNode['item']['content_code'];
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


    public function format(object $nodeItem): object
    {
        $nodeItem->output = unserialize($nodeItem->output);
        if ($nodeItem->output === '') {
            $nodeItem->output = false;
        }

        return $nodeItem;
    }

    private $dir = null;
    private ?\Closure $nameCodeFn = null;
    private ?\Closure $contentCodeFn = null;

    public function start(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $dir = Be::getRuntime()->getRootPath() . '/data/App/Etl/output_files/' . $flowNodeLog->id . '/files';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->dir = $dir;

        if ($flowNode->item->name === 'code') {
            try {
                $this->nameCodeFn = eval('return function(object $input): string {' . $flowNode->item->name_code . '};');
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 文件名代码处理（name_code）执行出错：' . $t->getMessage());
            }
        }

        if ($flowNode->item->content === 'code') {
            try {
                $this->contentCodeFn = eval('return function(object $input): string {' . $flowNode->item->content_code . '};');
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 文件名代码处理（name_code）执行出错：' . $t->getMessage());
            }
        }
    }


    public function process(object $flowNode, object $input, object $flowLog, object $flowNodeLog): object
    {
        $output = new \stdClass();

        if ($flowNode->item->name === 'template') {

            $name = $flowNode->item->name_template;
            foreach ((array)$input as $k => $v) {
                $name = str_replace('{' . $k . '}', $v, $name);
            }
            $output->name = $name;

        } else {
            try {
                $fn = $this->nameCodeFn;
                $output->name = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 文件名代码处理（name_code）执行出错：' . $t->getMessage());
            }
        }

        if ($flowNode->item->content === 'template') {
            $content = $flowNode->item->content_template;
            foreach ((array)$input as $k => $v) {
                $content = str_replace('{' . $k . '}', $v, $content);
            }
            $output->content = $content;

        } else {
            try {
                $fn = $this->contentCodeFn;
                $output->content = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . ($flowNode->index + 1) . ' 文件内容代码处理（content_code）执行出错：' . $t->getMessage());
            }
        }

        $path = $this->dir . '/' . $output->name;
        file_put_contents($path, $output->content);

        return $output;
    }


    public function finish(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $outputFile = '/data/App/Etl/output_files/' . $flowNodeLog->id . '/files.zip';

        $zip = new \ZipArchive();
        $zip->open(Be::getRuntime()->getRootPath() . $outputFile, \ZipArchive::CREATE);   //打开压缩包

        $handler = opendir($this->dir);
        while (($filename = readdir($handler)) !== false) {
            if ($filename !== '.' && $filename !== '..' && is_file($this->dir . '/' . $filename)) {
                $zip->addFile($this->dir . '/' . $filename, '/' . $filename);
            }
        }
        @closedir($handler);

        $zip->close();

        $flowNodeLog->output_file = $outputFile;
        $flowNodeLog->update_time = date('Y-m-d H:i:s');
        Be::getDb()->update('etl_flow_node_log', $flowNodeLog);

        \Be\Util\File\Dir::rm($this->dir);

        $this->nameCodeFn = null;
        $this->contentCodeFn = null;
    }


}
