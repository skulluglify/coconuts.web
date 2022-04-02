<?php namespace tiny;

// require first
use JetBrains\PhpStorm\Pure;

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

    public function drop(): bool
    {

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

    protected function insertRow(array $vars, array $values): bool
    {

        if (!empty($vars)) {

            $keys = array_keys($values);
            $names = [];
            $maps = [];
            $q = [];

            foreach ($vars as $name => $declare) {

                if (in_array($name, $keys)) {

                    $names[] = "`$name`";
                    $maps[] = $values[$name];
                    $q[] = "?";
                }
            }

            $context = join(",", $names);
            $qs = join(",", $q);

            $check = $this->connect->eval("INSERT INTO `$this->name`($context) VALUES ( $qs )", ...$maps);

            return $this->success($check);
        }

        return false;
    }

    protected function updateRow(array $values, array $wheres): bool
    {

        $contexts = $this->getContextBind($wheres, $values);

        $check = $this->connect->eval("UPDATE `$this->name` SET $contexts[0] WHERE $contexts[1]", ...$contexts[2]);

        return $this->success($check);
    }

    protected function deleteRow(array $wheres): bool
    {

        $contexts = $this->whereMaps($wheres);

        $check = $this->connect->eval("DELETE FROM `$this->name` WHERE $contexts[0]", ...$contexts[1]);

        return $this->success($check);
    }

    protected function selectRows(array $wheres, array | string | null $tables = null, int $size = 0, string $operators = "AND"): array | null
    {

        $contexts = $this->getContextSelector($wheres, $tables, $size, $operators);

        $fetch = $this->connect->eval("SELECT $contexts[0] FROM `$this->name` WHERE $contexts[1]", ...$contexts[2]);

        return $this->getResult($fetch);
    }

    protected function getContextVar(array $vars): array
    {

        if (!empty($vars)) {

            $maps = [];

            foreach ($vars as $name => $declare) {


                $maps[] = "`$name` $declare";
            }

            return [ join(",", $maps) ];
        }

        return [ "" ]; // none
    }

    protected function getContextBind(array $wheres, array $values, string $operators = "AND"): array
    {

        $maps = [];
        $names = [];

        foreach ($values as $key => $value) {

            // not null
            if (!empty($value)) {
                $maps[] = $value;
                $names[] = $key;
            }
        }

        $context = join(",", array_map(function ($key) {

            return "`$key` = ?";
        }, $names));

        // added
        $w = $this->whereMaps($wheres, $operators);
        array_push($maps, ...$w[1]);

        return [ $context, $w[0], $maps ];
    }

    protected function getContextSelector(array $wheres, array | string | null $tables = null, int $size = 0, string $operators = "AND"): array
    {

        $context = $this->tableMaps($tables);
        $w = $this->whereMaps($wheres, $operators);

        if ($size > 0) {

            // safety, +performance
            $w[0] .= " LIMIT $size";
        }

        return [ $context, $w[0], $w[1] ];
    }

    protected function tableMaps(array | string | null $tables): string
    {
        if (!is_null($tables)) {

            if (is_array($tables)) {

                if (!empty($tables)) {

                    // auto fit
                    if (!c($tables, 0)) $tables = array_keys($tables);

                    $keys = array_map(function ($v) {

                        return "`$v`";

                    }, $tables);
                    $qs = join(",", $keys);
                    return "$qs";
                }

            }

            if (is_string($tables)) {

                return $tables;
            }
        }

        return "*";
    }

    protected function whereMaps(array $wheres, string $operators = "AND"): array
    {

        $maps = array_map(function (string $key): string {

            // $key = str to lower($key);

            if (str_ends_with($key, "+")) {

                $key = rtrim(substr($key, 0, strlen($key) - 1));
                return "`$key` LIKE ?";

            } else {

                return "`$key` = ?";
            }
        }, array_keys($wheres));

        return [
            join(" $operators ", $maps),
            array_values($wheres)
        ];
    }

    protected function getResult(bool | null | MySQLFetchStructure $fetch, int $size = 0): array | null
    {

        if ($fetch instanceof MySQLFetch) {

            if (2 <= $size) {

                return $fetch->many($size);

            } else
                if (1 == $size) {

                    return $fetch->one();
                }

            return $fetch->all();
        }

        return null;
    }

    #[Pure] protected function success(bool | null | MySQLFetchStructure $check): bool
    {

        if (is_bool($check)) return $check or $this->connect->has_changed();
        if ($check instanceof MySQLFetch) return true;
        return false;
    }
}
