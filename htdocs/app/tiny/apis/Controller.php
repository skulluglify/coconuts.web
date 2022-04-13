<?php namespace tiny;


enum MessageType: int
{
    case SUCCESS = 0;
    case FAILURE = 1;
}


interface ControllerStructure
{
    public function lock(): bool;
}


abstract class Controller implements ControllerStructure
{

    protected Server $server;
    protected array $level;
    protected bool $wait;

    public function __construct(Server $server)
    {

        $this->wait = false;
        $this->server = $server;
    }

    protected abstract function init(): void;

    // only using ControllerBind
    // public static abstract function bind(MySQL $conn, Server $server, array $level = array()): mixed;


    protected function setLevel(array $level): void
    {

        $this->level = $level;
    }

    protected function trace(string $msg, array | null $assign = null, MessageType $mode = MessageType::SUCCESS): array
    {
        // $data = []; // unused

        $data = match ($mode) {
            MessageType::SUCCESS => array(
                "success" => array(
                    "message" => $msg
                )
            ),
            MessageType::FAILURE => array(
                "error" => array(
                    "message" => $msg
                )
            )
        };

        if (!is_null($assign)) $data = array_merge($data, $assign);

        return $data;
    }

    public function lock(): bool
    {

        return $this->wait;
    }
}