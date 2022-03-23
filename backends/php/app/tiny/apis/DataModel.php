<?php namespace tiny;

// require first
require_once "MySQL.php";


interface DataModelStructure // drop, create, insert, update, delete, select
{
    public function drop(): bool;
    public function create(): bool;
    public function insert(array $values): bool;
    public function update(array $values, array $wheres): bool;
    public function select(array $wheres, array | string | null $tables = null, int $size = 0): array | null;
    public function delete(array $wheres): bool;
}


abstract class DataModel implements DataModelStructure
{

    protected string $name;
    protected MySQL $connect;

    public function __construct(MySQL $conn, string $name)
    {

        $this->connect = $conn;
        $this->name = $name;
    }

    public function drop(): bool {

        if ($this->connect->eval("DROP TABLE IF EXISTS `$this->name`")) {

            return true;
        }

        return false;
    }

    public abstract function create(): bool;

    public abstract function insert(array $values): bool;

    public abstract function update(array $values, array $wheres): bool;

    public abstract function delete(array $wheres): bool;

    public abstract function select(array $wheres, array | string | null $tables = null, int $size = 0): array | null;

    protected function tableMaps(array | string | null $tables): string
    {
        if (!is_null($tables)) {

            if (is_array($tables)) {

                if (!empty($tables)) {


                    $keys = array_map(function ($v) {

                        return "`$v`";
                    }, array_keys($tables));
                    $qs = join(",", $keys);
                    return "($qs)";
                }

            }

            if (is_string($tables)) {

                return $tables;
            }
        }

        return "*";
    }

    protected function whereMaps(array $wheres): array {

        $maps = array_map(function (string $key): string {

            $key = strtolower($key);

            if (str_ends_with($key, "+")) {

                $key = rtrim(substr($key, 0, strlen($key) - 1));
                return "`$key` LIKE ?";

            } else {

                return "`$key` = ?";
            }
        }, array_keys($wheres));

        return [
            join(" AND ", $maps),
            array_values($wheres)
        ];
    }
}
