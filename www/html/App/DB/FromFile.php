<?php

namespace App\DB;

final class FromFile
{
    const FILE_PATH = "/var/www/html/data.json";

    /**
     * @var array
     */
    private static $instances = [];

    public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static;
        }
        return self::$instances[$subclass];
    }

    public function getData(string $table = null): array
    {
        $data = file_get_contents(self::FILE_PATH);
        $data = json_decode($data, true);
        if (!is_null($table)) {
            $data = $data[$table];
        }
        return $data;
    }

    public function updateData(string $table, int $id, array $newData)
    {
        $data = file_get_contents(self::FILE_PATH);
        $data = json_decode($data, true);
        $key = array_search($id, array_column($data[$table], "id"));
        $data[$table][$key] = $newData;
        $data = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(self::FILE_PATH, $data);
    }

    public function insertData(string $table, array $newData)
    {
        $data = file_get_contents(self::FILE_PATH);
        $data = json_decode($data, true);
        array_push($data[$table], $newData);
        $data = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(self::FILE_PATH, $data);
    }
}
