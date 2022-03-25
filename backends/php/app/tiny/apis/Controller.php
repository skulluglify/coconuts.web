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
    protected bool $wait;

    public function __construct(Server $server)
    {

        $this->wait = false;
        $this->server = $server;
    }

    protected abstract function init(): void;

    protected function setMessage(string $msg, array | null $assign = null, MessageType $mode = MessageType::SUCCESS): void
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

        $this->server->setDataJSON($data);
    }

    public function lock(): bool
    {

        return $this->wait;
    }
}