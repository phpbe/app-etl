<?php

namespace Be\App\Etl\Service;

use Be\System\Be;
use Be\System\db\Driver;
use Be\System\Exception\ServiceException;

class Ds extends \Be\System\Service
{

    private $dbs = [];

    /**
     * 获取指定数据源的表信息
     *
     * @param int $dsId
     * @return array
     */
    public function getTableNames($dsId)
    {
        $db = $this->getDb($dsId);
        return $db->getTableNames();
    }

    public function getTableFields($dsId, $tableName)
    {
        $db = $this->getDb($dsId);
        return array_values($db->getTableFields($tableName));
    }

    public function getSqlFields($dsId, $sql)
    {
        $db = $this->getDb($dsId);
        $arr = $db->getArray($sql);

        $fields = [];
        foreach ($arr as $key => $val) {
            $fields[] = [
                'name' => $key,
            ];
        }

        return $fields;
    }

    /**
     * 数据数据库连接
     *
     * @param int $dsId
     * @return Driver
     * @throws \Exception
     */
    public function getDb($dsId)
    {
        if (!isset($this->dbs[$dsId])) {
            $this->dbs[$dsId] = $this->newDb($dsId);
        }
        return $this->dbs[$dsId];
    }

    /**
     * 数据数据库连接
     *
     * @param int $dsId
     * @return Driver
     * @throws \Exception
     */
    public function newDb($dsId)
    {
        $ds = Be::getTuple('etl_ds')->load($dsId);
        $config = [
            'driver' => $ds->type,
            'host' => $ds->db_host,
            'port' => $ds->db_port,
            'username' => $ds->db_user,
            'password' => $ds->db_pass,
            'name' => $ds->db_name,
        ];

        // Oracle 使用长连接避免中断
        if ($ds->type == 'oracle') {
            $config['options'] = [
                \PDO::ATTR_PERSISTENT => TRUE,
            ];
        }

        $class = 'Be\\System\\Db\\Driver\\' . ucfirst($config['driver']) . 'Impl';
        if (!class_exists($class)) throw new ServiceException('数据源（' . $ds->name . '）指定的数据库驱动' . $ds->type . '不支持！');

        $db = new $class($config);
        $db->connect();
        return $db;
    }

    public function getIdNameKeyValues()
    {
        return Be::getTable('etl_ds')
            ->where('is_delete', 0)
            ->where('is_enable', 1)
            ->getKeyValues('id', 'name');
    }

    public function testDb($data)
    {
        $type = $data['type'];
        $host = $data['db_host'];
        $port = $data['db_port'];
        $user = $data['db_user'];
        $pass = $data['db_pass'];

        $options = array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        );

        if (!in_array($type, ['mysql','mssql','oracle'])) {
            throw new ServiceException('不支持的数据库类型：' . $type);
        }

        $connection = null;
        try {
            switch ($type) {
                case 'mysql':
                    $dsn = 'mysql:host=' . $host . ';port=' . $port;
                    $connection = new \PDO($dsn, $user, $pass, $options);
                    break;
                case 'mssql':
                    $dsn = 'sqlsrv:Server=' . $host . ',' . $port;
                    $connection = new \PDO($dsn, $user, $pass, $options);
                    break;
                case 'oracle':
                    $dsn = 'oci:dbname=//' . $host . ':' . $port . '/';
                    $connection = new \PDO($dsn, $user, $pass, $options);
                    break;
            }
        } catch (\Throwable $t) {
            throw new ServiceException('连接数据库失败：' . $t ->getMessage());
        }

        $sql = null;
        switch ($type) {
            case 'mysql':
                $sql = 'SELECT `SCHEMA_NAME` FROM information_schema.SCHEMATA WHERE `SCHEMA_NAME`!=\'information_schema\'';
                break;
            case 'mssql':
                $sql = 'SELECT [name] FROM master..sysdatabases WHERE [name]!=\'master\'';
                break;
            case 'oracle':
                $sql = 'SELECT * FROM v$tablespace';
                break;
        }

        $values = null;
        try {
            $statement = $connection->query($sql);
            $values = $statement->fetchAll(\PDO::FETCH_COLUMN);
            $statement->closeCursor();
        } catch (\Throwable $t) {
            throw new ServiceException('连接数据库成功，但获取库名列表失败：' . $t ->getMessage());
        }

        return $values;
    }


}
