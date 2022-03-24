<?php namespace controllers;


use models\Banned;
use models\Session;
use models\User;
use tiny\Date;
use tiny\MySQL;
use tiny\Server;
use function tiny\c;


class Login
{

    protected MySQL $connect;
    protected array $level;

    // tables
    protected User $user;
    protected Session $session;
    protected Banned $banned;

    // server
    protected Server $server;

    // running
    protected bool $wait = false;

    public function __construct(MySQL $conn, Server $server, array $levels)
    {

        $this->connect = $conn;
        $this->server = $server;
        $this->level = $levels;

        $this->init();
    }

    protected function init(): void
    {

        $this->user = new User($this->connect);
        $this->session = new Session($this->connect);
        $this->banned = new Banned($this->connect);

        // create tables if exists, debug drop it
        // $this->user->create();
        // $this->session->create();
         $this->banned->create();

        $data = $this->server->getDataJSON();

        // check data json
        if (is_object($data)) {

            // get commands key
            if (property_exists($data, "login")) {

                $this->wait = true;

                $login = $data->login;
                $user_uniq = property_exists($login, "user_uniq") ? $login->user_uniq : null;
                $user_email = property_exists($login, "user_email") ? $login->user_email : null;
                $user_pass = property_exists($login, "user_pass") ? $login->user_pass : null;

                $attach = true;
                while ($attach)
                {

                    // get try login
                    $user_id = -1;
                    $try_login = -1;

                    if (!is_null($user_uniq)) {

                        $user_id = $this->user->getId(array(
                            "user_uniq" => $user_uniq
                        ));

                    } else if (!is_null($user_email)) {

                        $user_id = $this->user->getId(array(
                            "user_email" => $user_email
                        ));
                    }

                    if ($user_id < 0) {

                        $this->errorMessage("var user_name, user_email not found!");
                        break;
                    }

                    $data = $this->session->select(array(
                        "user_id" => $user_id
                    ), "try_login", 1);

                    if (!is_null($data)) {

                        $v = c($data, 0, "try_login");

                        if (!is_null($v)) {

                            if (!is_int($v)) $v = intval($v);

                            $try_login = $v;
                        }
                    }

                    $data = $this->banned->select(array(
                        "user_id" => $user_id
                    ), ["level", "time"], 1);

                    $level = -1;

                    if (!empty($data)) {

                        // $ip = c($data, 0, "user_ip");
                        // $agent = c($data, 0, "user_agent");
                        $level = c($data, 0, "level");
                        $timestamp = c($data, 0, "time");

                        // get level time
                        if (!is_null($level) and !is_null($timestamp)) {

                            if (!is_int($level)) $level = intval($level);
                            if (!is_int($timestamp)) $timestamp = strtotime($timestamp);

                            if (0 <= $level) {

                                $start = (new Date())->getTimestamp();
                                $end = Date::enhance_time_ms($this->level[$level], $timestamp);

                                if ($start < $end) {

                                    $this->errorMessage("You are blocked, due to unnatural traffic activity!"); // yes u got ban, ha ha ha
                                    break;

                                // } else {

                                    // can log in
                                    // if failed again, ban level has been increase

                                }
                            }
                        } else {

                            $this->errorMessage("something went wrong on the server!");
                            break;
                        }
                    }

                    // 2 <= 3 times
                    // set ban if try_login equals great then 3
                    if (3 <= $try_login) {

                        $check = $this->banned->select(array(
                            "user_id" => $user_id
                        ), size: 1);

                        if (!empty($check)) {

                            $this->banned->update(array(
                                "level" => 0 <= $level ? $level + 1 : 0
                            ), array(
                                "user_id" => $user_id
                            ));

                        } else {

                            $this->banned->insert(array(
                                "user_id" => $user_id,
                                "user_ip" => $this->server->getClientIP(),
                                "user_agent" => $this->server->HTTP->getUserAgent(),
                                "level" => 0
                            ));
                        }

                        $this->session->update(array(
                            "try_login" => 1
                        ), array(
                            "user_id" => $user_id
                        ));

                        $this->errorMessage("You are blocked, for exceeding the login limit!");
                        break;
                    }

                    // check pass
                    $pass = null;

                    $data = $this->user->select(array(
                        "user_uniq" => $user_uniq,
                        "user_email" => $user_email
                    ), "user_pass", 1, "OR");

                    if (!empty($data)) {

                        $pass = c($data, 0, "user_pass");
                    }

                    if ($user_pass == $pass) {

                        $token = Session::createToken($user_uniq or $user_email);

                        $this->successMessage("success login", array(
                            "token" => $token
                        ));

                        // delete banned
                        $this->banned->delete(array(
                            "user_id" => $user_id
                        ));

                        // delete session
                        $this->session->delete(array(
                            "user_id" => $user_id
                        ));

                        // create new session
                        $this->session->insert(array(
                            "user_id" => $user_id,
                            "user_ip" => $this->server->getClientIP(),
                            "user_agent" => $this->server->HTTP->getUserAgent(),
                            "try_login" => 1,
                            "token" => $token
                        ));

                    } else {

                        // inc try_token
                        // try update, insert
                        if (0 <= $try_login) {

                            $this->session->update(array(
                                "try_login" => $try_login + 1,
                                "token" => "" // delete token
                            ), array(
                                "user_id" => $user_id
                            ));

                        } else {

                            $this->session->insert(array(
                                "user_id" => $user_id,
                                "user_ip" => $this->server->getClientIP(),
                                "user_agent" => $this->server->HTTP->getUserAgent(),
                                "try_login" => 1,
                                "token" => "" // delete token
                            ));
                        }

                        $this->errorMessage("failed login!");
                        break;
                    }

                    $attach = false;
                }
            }
        }
    }

    protected function successMessage(string $msg, array | null $assign = null): void
    {

        $data = array(
            "success" => array(
                "message" => $msg
            )
        );

        if (!is_null($assign)) $data = array_merge($data, $assign);

        echo json_encode($data);
    }

    protected function errorMessage(string $msg, array | null $assign = null): void
    {

        $data = array(
            "error" => array(
                "message" => $msg
            )
        );

        if (!is_null($assign)) $data = array_merge($data, $assign);

        echo json_encode($data);
    }

    public function lock(): bool
    {
        return $this->wait;
    }
}