<?php namespace models;


use JetBrains\PhpStorm\Pure;
use tiny\DataModel;
use tiny\DataModelStructure;
use tiny\MySQL;
use function tiny\c;


class User extends DataModel implements DataModelStructure
{
    protected MySQL $connect;

    protected array $vars = array(
        "id" => "INT AUTO_INCREMENT",
        "user_photo" => "TEXT",
        "user_name" => "TEXT NOT NULL",
        "user_uniq" => "TEXT NOT NULL",
        "user_age" => "DATE NOT NULL",
        "user_gender" => "TEXT NOT NULL",
        "user_email" => "TEXT NOT NULL",
        "user_pass" => "TEXT NOT NULL",
        "user_phone" => "TEXT",
        "user_location" => "TEXT",
        "user_description" => "TEXT"
    );

    #[Pure] public function __construct(MySQL $conn)
    {

        parent::__construct($conn, "users");
    }

    public function create(): bool
    {
        $contexts = $this->getContextVar($this->vars);

        $check = $this->connect->eval("
            CREATE TABLE IF NOT EXISTS `$this->name`(
                $contexts[0],
                time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(`id`)
            )
        ");

        return $this->success($check);
    }

    public function insert(array $values): bool
    {

        return $this->insertRow($this->vars, $values);
    }

    public function update(array $values, array $wheres): bool
    {

        return $this->updateRow($values, $wheres);
    }

    public function delete(array $wheres): bool {

        return $this->deleteRow($wheres);
    }

    public function select(array $wheres, array | string | null $tables = null, int $size = 0, string $operators = "AND"): array | null
    {

        return $this->selectRows($wheres, $tables, $size, $operators);
    }

    // utilities
    public function getId(array $wheres): int | null
    {

        $data = $this->select($wheres, "id", 1);

        if (!empty($data)) {

            $v = c($data, 0, "id");
            if (is_int($v)) return intval($v);
        }

        return null;
    }
}
