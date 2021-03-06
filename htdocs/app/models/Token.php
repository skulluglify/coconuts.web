<?php namespace models;


use JetBrains\PhpStorm\Pure;
use tiny\DataModel;
use tiny\DataModelStructure;
use tiny\MySQL;

// SUMMARY: belum digunakan karena masih ragu ragu untuk memisahkan token dari tabel session


class Token extends DataModel implements DataModelStructure
{

    protected MySQL $connect;

    protected array $vars = array(
        "id" => "INT AUTO_INCREMENT",
        "user_id" => "INT",
        "x_user_id" => "INT",
        "token" => "TEXT"
    );

    #[Pure] public function __construct(MySQL $conn)
    {
        parent::__construct($conn, "tokens");
    }

    public function create(): bool
    {
        $context = $this->getContextVar($this->vars);

        $check = $this->connect->eval("
            CREATE TABLE IF NOT EXISTS `$this->name`(
                $context[0],
                INDEX `x_user_id`(`user_id`),
                UNIQUE (`token`),
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE RESTRICT ON DELETE CASCADE,
                time TIMESTAMP DEFAULT UTC_TIMESTAMP,
                PRIMARY KEY (`id`)
            )");

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

    public function delete(array $wheres): bool
    {
        return $this->deleteRow($wheres);
    }

    public function select(array $wheres, array | string | null $tables = null, int $size = 0): array | null
    {
        return $this->selectRows($wheres, $tables, $size);
    }
}