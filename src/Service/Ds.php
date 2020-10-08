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
            $ds = Be::getTuple('etl_ds')->load($dsId);
            $config = [
                'driver' => $ds->type,
                'host' => $ds->db_host,
                'port' => $ds->db_port,
                'user' => $ds->db_user,
                'pass' => $ds->db_pass,
                'name' => $ds->db_name,
            ];

            $class = 'Be\\System\\Db\\Driver\\' . $config['driver'] . 'Impl';
            if (!class_exists($class)) throw new ServiceException('数据源（' . $ds->name . '）指定的数据库驱动' . $ds->type . '不支持！');

            $db = new $class($config);
            $db->connect();
            $this->dbs[$dsId] = $db;
        }
        return $this->dbs[$dsId];
    }

    public function getIdNameKeyValues()
    {
        return Be::getTable('etl_ds')
            ->where('is_delete', 0)
            ->where('is_enable', 1)
            ->getKeyValues('id', 'name');
    }


    public function connect($type, $host, $port, $user, $pass)
    {
        $options = array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        );

        switch ($type) {
            case 'mysql':
                $dsn = 'mysql:host=' . $host . ';port=' . $port;
                return new \PDO($dsn, $user, $pass, $options);
                break;
            case 'mssql':
                $dsn = 'sqlsrv:Server=' . $host .',' . $port;
                return new \PDO($dsn, $user, $pass, $options);
                break;
            case 'oracle':
                $dsn = 'oci:dbname=//' . $host .  ':' . $port . '/';
                return new \PDO($dsn, $user, $pass, $options);
                break;
        }
    }

    public function getDatabases($type, $host, $port, $user, $pass) {
        $connection = $this->connect($type, $host, $port, $user, $pass);
        switch ($type) {
            case 'mysql':
                return $this->getValues('SELECT `SCHEMA_NAME` FROM information_schema.SCHEMATA WHERE `SCHEMA_NAME`!=\'information_schema\'');
                break;
            case 'mssql':
                return $this->getValues('SELECT [name] FROM master..sysdatabasesWHERE [name]!=\'master\'');
                break;
            case 'oracle':


                break;
        }


    }



}
