<?php


namespace App\Core\DB;

use App\Core\Application;
use App\Core\BaseModel;

abstract class DBModel extends BaseModel
{
    abstract public function tableName() : string;

    abstract public function attributes() : array;

    abstract function primaryKey() : string;

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $sql = "INSERT INTO $tableName (".implode(',', $attributes).")
        VALUES (".implode(',', $params).")";

        $statement = self::prepare($sql);

        foreach ($attributes as $attribute){
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
        return true;
    }

    public static function prepare($sql)
    {
        return Application::$APP->db->pdo->prepare($sql);
    }

    public  function findOne($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $params = implode("AND ",array_map(fn($attr) => "$attr = :$attr", $attributes));

        $sql = "SELECT * FROM $tableName WHERE $params";
        $statement = self::prepare($sql);
        foreach ($where as $key => $item){
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
    }
}