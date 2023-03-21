<?php

namespace Be\App\Etl\Service\Admin\FlowNode\Output;


use Be\App\Etl\Service\Admin\FlowNode\Output;
use Be\App\ServiceException;
use Be\Be;

class Folders extends Output
{

    public function getItemName(): string
    {
        return '目录包';
    }

    /**
     * 编辑数据流
     *
     * @param array $formDataNode 表单数据
     * @param object $input 输入数据
     * @return object
     * @throws \Throwable
     */
    public function test(array $formDataNode, object $input): object
    {
        if (!isset($formDataNode['index']) || !is_numeric($formDataNode['index'])) {
            throw new ServiceException('节点参数（index）无效！');
        }

        if (!isset($formDataNode['item']['name']) || !is_string($formDataNode['item']['name']) || !in_array($formDataNode['item']['name'], ['template', 'code'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 目录名处理方式（name）参数无效！');
        }


        $output = new \stdClass();

        if ($formDataNode['item']['name'] === 'template') {

            if (!isset($formDataNode['item']['name_template']) || !is_string($formDataNode['item']['name_template']) || $formDataNode['item']['name_template'] !== '') {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 目录名模板（name_template）参数无效！');
            }

            $name = $formDataNode['item']['name_template'];
            foreach ((array)$input as $k => $v) {
                $name = str_replace('{' . $k . '}', $v, $name);
            }
            $output->name = $name;

        } else {

            if (!isset($formDataNode['item']['name_code']) || !is_string($formDataNode['item']['name_code']) || $formDataNode['item']['name_code'] !== '') {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 目录代码处理（name_code）参数无效！');
            }

            try {
                $fn = eval('return function(object $input): string {' . $formDataNode['item']['name_code'] . '};');
                $output->name = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 目录代码处理（name_code）执行出错：' . $t->getMessage());
            }

        }

        if (!isset($formDataNode['item']['files']) || !is_array($formDataNode['item']['files'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 文件列表（files）参数无效！');
        }

        if (count($formDataNode['item']['files']) > 0) {

            $files = [];

            $i = 1;
            foreach ($formDataNode['item']['files'] as $file) {

                if (!isset($file['name_template']) || !is_string($file['name_template']) || $file['name_template'] !== '') {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 文件 ' . $i . ' 文件名称模板（name_template）参数无效！');
                }

                $name = $file['name_template'];
                foreach ((array)$input as $k => $v) {
                    $name = str_replace('{' . $k . '}', $v, $name);
                }

                if (!isset($file['content_template']) || !is_string($file['content_template']) || $file['content_template'] !== '') {
                    throw new ServiceException('节点 ' . $formDataNode['index'] . ' 文件 ' . $i . ' 文件内容模板（content_template）参数无效！');
                }

                $content = $file['content_template'];
                foreach ((array)$input as $k => $v) {
                    $content = str_replace('{' . $k . '}', $v, $content);
                }

                $files[$name] = $content;

                $i++;
            }

            $output->files = $files;
        }

        if (!isset($formDataNode['item']['files_code']) || !is_string($formDataNode['item']['files_code'])) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 代码输出文件列表（files_code）参数无效！');
        }

        if ($formDataNode['item']['files_code'] !== '') {
            try {
                $fn = eval('return function(object $input): array {' . $formDataNode['item']['files_code'] . '};');
                $output->files_code = $fn($input);
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $formDataNode['index'] . ' 代码输出文件列表（files_code）执行出错：' . $t->getMessage());
            }
        }

        $filesCount = 0;
        if (isset($output->files)) {
            $filesCount += count($output->files);
        }

        if (isset($output->files_code)) {
            $filesCount += count($output->files_code);
        }

        if ($filesCount === 0) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 输出文件列表为空！');
        }

        if (count((array)$output) === 0) {
            throw new ServiceException('节点 ' . $formDataNode['index'] . ' 输出数据为空！');
        }

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
        $tupleFlowNodeItem = Be::getTuple('etl_flow_node_output_folders');
        $tupleFlowNodeItem->flow_node_id = $flowNodeId;
        $tupleFlowNodeItem->name = $formDataNode['item']['name'];
        $tupleFlowNodeItem->name_template = $formDataNode['item']['name_template'];
        $tupleFlowNodeItem->name_code = $formDataNode['item']['name_code'];
        $tupleFlowNodeItem->files = serialize($formDataNode['item']['files']);
        $tupleFlowNodeItem->files_code = $formDataNode['item']['files_code'];
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
        $nodeItem->files = unserialize($nodeItem->files);

        $nodeItem->output = unserialize($nodeItem->output);
        if ($nodeItem->output === '') {
            $nodeItem->output = false;
        }

        return $nodeItem;
    }

    private $dir = null;
    private ?\Closure $nameCodeFn = null;
    private ?array $files = null;
    private ?\Closure $filesCodeFn = null;


    public function start(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $dir = Be::getRuntime()->getRootPath() . '/data/App/Etl/Output/Folders/' . $flowNodeLog->id . '/folders';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->dir = $dir;

        if ($flowNode->item->name === 'code') {
            try {
                $this->nameCodeFn = eval('return function(object $input): string {' . $flowNode->item->name_code . '};');
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $flowNode->index . ' 文件名代码处理（name_code）执行出错：' . $t->getMessage());
            }
        }

        $this->files = unserialize($flowNode->item->files);

        if ($flowNode->item->files_code !== '') {
            try {
                $this->filesCodeFn = eval('return function(object $input): array {' . $flowNode->item->files_code . '};');
            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $flowNode->index . ' 代码输出文件列表（files_code）执行出错：' . $t->getMessage());
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
                throw new ServiceException('节点 ' . $flowNode->index . ' 文件名代码处理（name_code）执行出错：' . $t->getMessage());
            }
        }

        $dir = $this->dir . '/' . $output->name;
        if (is_dir($dir)) {
            throw new ServiceException('节点 ' . $flowNode->index . ' 文件名代码处理（name_code）目录（' . $dir . '）已存在：');
        }

        mkdir($dir, 0777, true);

        if (count($this->files) > 0) {

            $files = [];

            foreach ($this->files as $file) {

                $name = $file['name_template'];
                foreach ((array)$input as $k => $v) {
                    $name = str_replace('{' . $k . '}', $v, $name);
                }

                $content = $file['content_template'];
                foreach ((array)$input as $k => $v) {
                    $content = str_replace('{' . $k . '}', $v, $content);
                }

                $path = $dir . '/' . $name;
                file_put_contents($path, $content);

                $files[$name] = $content;
            }

            $output->files = $files;
        }

        if ($flowNode->item->files_code !== '') {
            try {
                $fn = $this->filesCodeFn;
                $codeFiles = $fn($input);

                if (is_array($codeFiles) && count($codeFiles) > 0) {

                    foreach ($codeFiles as $name => $content) {
                        $path = $dir . '/' . $name;
                        file_put_contents($path, $content);
                    }

                    $output->files_code = $codeFiles;
                }

            } catch (\Throwable $t) {
                throw new ServiceException('节点 ' . $flowNode->index . ' 代码输出文件列表（files_code）执行出错：' . $t->getMessage());
            }
        }

        return $output;
    }


    public function finish(object $flowNode, object $flowLog, object $flowNodeLog)
    {
        $outputFile = '/data/App/Etl/Output/Folders/' . $flowNodeLog->id . '/folders.zip';

        $zip = new \ZipArchive();
        $zip->open(Be::getRuntime()->getRootPath() . $outputFile, \ZipArchive::CREATE);   //打开压缩包

        $handler = opendir($this->dir);
        while (($dir = readdir($handler)) !== false) {
            if ($dir !== '.' && $dir != '.' && is_dir($this->dir . '/' . $dir)) {
                $files = scandir($this->dir . '/' . $dir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..' && is_file($this->dir . '/' . $dir . '/' . $file)) {
                        $zip->addFile($this->dir . '/' . $dir . '/' . $file, '/' . $dir . '/' . $file);
                    }
                }
            }
        }
        @closedir($handler);

        $zip->close();

        $flowNodeLog->output_file = $outputFile;
        $flowNodeLog->update_time = date('Y-m-d H:i:s');
        Be::getDb()->update('etl_flow_node_log', $flowNodeLog);

        \Be\Util\File\Dir::rm($this->dir);

        $this->nameCodeFn = null;
        $this->filesCodeFn = null;
    }


}
