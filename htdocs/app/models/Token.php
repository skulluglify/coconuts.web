<?php namespace models;


use JetBrains\PhpStorm\Pure;
use tiny\DataModel;
use tiny\DataModelStructure;
use tiny\MySQL;


class Token extends DataModel implements DataModelStructure
{

    protected MySQL $connect;

    #[Pure] public function __construct(MySQL $conn)
    {
        parent::__construct($conn, "tokens");
    }

    public function create(): bool
    {
        // TODO: Implement create() method.
        return false;
    }

    public function insert(array $values): bool
    {
        // TODO: Implement insert() method.
        return false;
    }

    public function update(array $values, array $wheres): bool
    {
        // TODO: Implement update() method.
        return false;
    }

    public function delete(array $wheres): bool
    {
        // TODO: Implement delete() method.
        return false;
    }

    public function select(array $wheres, array | string | null $tables = null, int $size = 0): array | null
    {
        // TODO: Implement select() method.
        return null;
    }
}