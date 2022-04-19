<?php namespace tiny;


interface ControllerBindStructure
{
    public static function bind(MySQL $conn, Server $server, array $level): mixed;
    public function lock(): bool;
}


abstract class ControllerBind extends Controller implements ControllerBindStructure
{

    protected MySQL $connect;

    protected function init(): void
    {
        // TODO: Implement init() method.
    }

    public static abstract function bind(MySQL $conn, Server $server, array $level = array()): mixed;
    // public static function bind(MySQL $conn, Server $server, array $level = array()): mixed
    // {

        // $bind = new self($server);
        // $bind->setMySQL($conn);
        // $bind->setLevel($level);
        // return $bind;
    // }

    protected function setMySQL(MySQL $conn): void
    {

        $this->connect = $conn;
    }
}