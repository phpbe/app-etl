<?php
namespace Be\App\Etl;

use Be\Be;

/**
 * 应用安装器
 */
class UnInstaller extends \Be\App\UnInstaller
{

    /**
     * 安装时需要执行的操作，如创建数据库表
     */
	public function uninstall()
	{
        $db = Be::getDb();

        $sql = file_get_contents(__DIR__ . '/UnInstaller.sql');
        $sqls = preg_split('/; *[\r\n]+/', $sql);
        foreach ($sqls as $sql) {
            $sql = trim($sql);
            if ($sql) {
                $db->query($sql);
            }
        }
	}

}
