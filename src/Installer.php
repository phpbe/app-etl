<?php
namespace Be\App\Etl;

/**
 * 应用安装器
 */
class Installer extends \Be\App\Installer
{

    /**
     * 安装时需要执行的操作，如创建数据库表
     */
	public function install()
	{
        $db = \Be\Be::getDb();
        $tableNames = $db->getTableNames();
        if (in_array('etl_ds', $tableNames)) {
            if (in_array('etl_flow_node_process_code', $tableNames)) {
                $installed = true;
            } else {
                throw new \Be\Runtime\RuntimeException('剑测到部分数据表已存在，请检查数据库！');
            }
        }

        if (!$installed) {
            $sql = file_get_contents(__DIR__ . '/exe/install/install.sql');
            $sqls = preg_split('/; *[\r\n]+/', $sql);
            foreach ($sqls as $sql) {
                $sql = trim($sql);
                if ($sql) {
                    $db->query($sql);
                }
            }
        }
	}

}
