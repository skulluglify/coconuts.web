<?php namespace models;


use JetBrains\PhpStorm\Pure;
use tiny\DataModel;
use tiny\DataModelStructure;
use tiny\MySQL;
use tiny\MySQLFetch;


class User extends DataModel implements DataModelStructure
{
    protected MySQL $connect;

    #[Pure] public function __construct(MySQL $conn)
    {

        parent::__construct($conn, "users");
        $this->connect = $conn;
    }

    public function create(): bool
    {

        $check = $this->connect->eval("
            CREATE TABLE IF NOT EXISTS `$this->name`(
                `user_id` INT AUTO_INCREMENT,
                `user_photo` TEXT,
                `user_name` TEXT NOT NULL,
                `user_uniq` TEXT NOT NULL,
                `user_age` DATE NOT NULL,
                `user_gender` TEXT NOT NULL,
                `user_email` TEXT NOT NULL ,
                `user_pass` TEXT NOT NULL,
                `user_phone` TEXT,
                `user_location` TEXT,
                `user_description` TEXT,
                time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(`user_id`)
            )
        ");

        if (is_bool($check)) return $check;
        if ($check instanceof MySQLFetch) return true;
        return false;
    }

    public function insert(array $values): bool
    {

        $check = $this->connect->eval("
            INSERT INTO `$this->name`(
                `user_photo`,
                `user_name`,
                `user_uniq`,
                `user_age`,
                `user_gender`,
                `user_email` ,
                `user_pass`,
                `user_phone`,
                `user_location`,
                `user_description`
            ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
        ",
            $values["user_photo"],
            $values["user_name"],
            $values["user_uniq"],
            $values["user_age"],
            $values["user_gender"],
            $values["user_email"],
            $values["user_pass"],
            $values["user_phone"],
            $values["user_location"],
            $values["user_description"]
        );

        if (is_bool($check)) return $check;
        if ($check instanceof MySQLFetch) return true;
        return false;
    }

    public function update(array $values, array $wheres): bool
    {

        $maps = [];
        $names = [];

        foreach ($values as $key => $value) {

            if (!is_null($value)) {
                $maps[] = $value;
                $names[] = $key;
            }
        }

        $context = join(",", array_map(function ($key) {

            return "`$key` = ?";
        }, $names));

        // added
        $w = $this->whereMaps($wheres);
        $where_maps = $w[0];
        array_push($maps, ...$w[1]);

        $check = $this->connect->eval("UPDATE `$this->name` SET $context WHERE $where_maps", ...$maps);

        if (is_bool($check)) return $check;
        if ($check instanceof MySQLFetch) return true;
        return false;
    }

    public function delete(array $wheres): bool {

        $maps = [];

        $w = $this->whereMaps($wheres);
        $where_maps = $w[0];
        array_push($maps, ...$w[1]);

        $check = $this->connect->eval("DELETE FROM `$this->name` WHERE $where_maps", ...$maps);
        if (is_bool($check)) return $check;
        if ($check instanceof MySQLFetch) return true;
        return false;
    }

    public function select(array $wheres, array | string | null $tables = null, int $size = 0): array | null
    {

        $maps = [];

        $table_maps = $this->tableMaps($tables);
        $w = $this->whereMaps($wheres);
        $where_maps = $w[0];
        array_push($maps, ...$w[1]);

        $size_maps = "";
        if ($size > 0) {

            // safety, +performance
            $size_maps = "LIMIT $size";
        }

        $d = $this->connect->eval("SELECT $table_maps FROM `$this->name` WHERE $where_maps $size_maps", ...$maps);

        if (2 <= $size) {

            return $d->many($size);

        } else
        if (1 == $size) {

            return $d->one();
        }

        return $d->all();
    }
}
