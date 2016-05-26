<?php namespace App\Models;

// 使用动态表(集合)名
// 使用方式：
//    $model = App\Models\Mongo\Model::fromTable($tableName);
//    $instance = $model->first();

trait DynamicTableName {
    protected static $_table;

    public static function fromTable($table, $params = Array())
    {
        $ret = null;
        if (class_exists($table)) {
            $ret = new $table($params);
        } else {
            $ret = new static($params);
            $ret->setTable($table);
        }
        return $ret;
    }

    public function setTable($table)
    {
        static::$_table = $table;
    }

    public function getTable()
    {
        return static::$_table;
    }
}
