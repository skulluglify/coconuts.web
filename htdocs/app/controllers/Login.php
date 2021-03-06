<?php namespace controllers;


session_start();
if (empty($_SESSION["blocked"])) $_SESSION["blocked"] = false;
if (empty($_SESSION["try_login"])) $_SESSION["try_login"] = 0;


use models\Banned;
use models\Session;
use models\User;
use tiny\Controller;
use tiny\ControllerBind;
use tiny\ControllerBindStructure;
use tiny\Date;
use tiny\MessageType;
use tiny\MySQL;
use tiny\Request;
use tiny\Response;
use tiny\Server;
use function tiny\c;
use function tiny\createToken;


class Login extends ControllerBind implements ControllerBindStructure
{

    protected MySQL $connect;

    // tables
    protected User $user;
    protected Session $session;
    protected Banned $banned;

    public function __construct(MySQL $conn, Server $server)
    {

        parent::__construct($server);
        $this->connect = $conn;
    }

    public static function bind(MySQL $conn, Server $server, array $level = array()): mixed
    {
        $bind = new self($conn, $server);
        $bind->setLevel($level);
        $bind->init();
        return $bind;
    }

    protected function init(): void
    {

        $this->user = new User($this->connect);
        $this->session = new Session($this->connect);
        $this->banned = new Banned($this->connect);

        // create tables if exists, debug drop it
        $this->user->create();
        $this->session->create();
        $this->banned->create();

        if (empty($this->level)) die("level not setup!");

        $this->server->route("login", function (Request $req, Response $res) {

            $this->wait = true;

            $data = $req->json();
            $res->header("Content-Type: application/json");

            // check data json
            if (!empty($data)) {

                // get commands key
                $login = c($data, "login");
                $try_login_session_limit = 3;

                if (!empty($login)) {

                    $user_uniq = c($login, "user_uniq");
                    $user_email = c($login, "user_email");
                    $user_pass = c($login, "user_pass");

                    $attach = true;
                    while ($attach)
                    {

                        $blocked = $_SESSION["blocked"];
                        if (is_bool($blocked) and $blocked) {

                            $res->render($this->trace("You are blocked, due to abnormal traffic activity!", mode: MessageType::FAILURE)); // yes u got ban, ha ha ha
                            break;
                        }

                        // get try login
                        $user_id = -1;
                        $try_login = -1;
                        $level = -1;

                        if (!empty($user_uniq)) {

                            $user_id = $this->user->getId(array(
                                "user_uniq" => $user_uniq
                            ));

                        } else if (!empty($user_email)) {

                            $user_id = $this->user->getId(array(
                                "user_email" => $user_email
                            ));
                        }

                        $ip = $this->server->getClientIP();
                        $userAgent = $this->server->HTTP->getUserAgent();

                        if (is_null($user_id) or $user_id < 0) {

                            // Make it Limit
                            // Supaya Tidak terjadi Brute force
                            $try_login_session = $_SESSION["try_login"];

                            // Not Good idea
                            $session_try_login = $this->session->select(array(
                                "user_ip" => $ip,
                                "user_agent" => $userAgent,
                            ), "try_login", 1);

                            if (!is_null($session_try_login)) {

                                $try_login_session = intval($session_try_login[0]["try_login"]);

                                // banned
                                $bans = $this->banned->select(array(
                                    "user_ip" => $ip,
                                    "user_agent" => $userAgent
                                ), ["level", "time"], 1);

                                if (!empty($bans)) {

                                    $level = c($bans, 0, "level");
                                    $timestamp = c($bans, 0, "time");
                                    // get level time
                                    if (!is_null($level) and !is_null($timestamp)) {

                                        if (!is_int($level)) $level = intval($level);
                                        if (!is_int($timestamp)) $timestamp = strtotime($timestamp);

                                        if (0 <= $level) {

                                            $start = (new Date())->getTimestamp();
                                            $end = Date::enhance_time_ms($this->level[$level], $timestamp);

                                            if ($start < $end) {

                                                $res->render($this->trace("You are blocked, due to abnormal traffic activity!", mode: MessageType::FAILURE)); // yes u got ban, ha ha ha
                                                break;
                                            }
                                        }
                                    }
                                }

                                if ($try_login_session_limit <= $try_login_session) {

                                    if (!empty($bans)) {

                                        $this->banned->update(array(
                                            "level" => 0 <= $level ? $level + 1 : 0,
                                            "time" => Date::enhance_time(0, (new Date())->getTimestamp())
                                        ), array(
                                            "user_ip" => $ip,
                                            "user_agent" => $userAgent
                                        ));

                                    } else {

                                        $this->banned->insert(array(
                                            "user_ip" => $ip,
                                            "user_agent" => $userAgent,
                                            "level" => 0
                                        ));
                                    }

                                    $res->render($this->trace("You are blocked, for exceeding the login limit!", mode: MessageType::FAILURE));
                                    break;
                                }

                                $this->session->update(array(
                                    "user_ip" => $ip,
                                    "user_agent" => $userAgent,
                                    "try_login" => 1 + $try_login_session,
                                    "token" => "" // delete token
                                ), array(
                                    "user_ip" => $ip,
                                    "user_agent" => $userAgent,
                                ));

                            } else {

                                $this->session->insert(array(
                                    "user_ip" => $ip,
                                    "user_agent" => $userAgent,
                                    "try_login" => 1,
                                    "token" => "" // delete token
                                ));
                            }
                            // Not Good idea

                            if (is_int($try_login)) {

                                if ($try_login_session_limit <= $try_login_session) $_SESSION["blocked"] = true;
                                $_SESSION["try_login"] += 1;
                            } else {

                                $_SESSION["try_login"] = 1;
                            }

                            $res->render($this->trace("var user_uniq, user_email not found!", mode: MessageType::FAILURE));
                            break;
                        }

                        if (empty($user_pass)) {
                            $res->render($this->trace("var user_pass cannot be empty!", mode: MessageType::FAILURE));
                            break;
                        }

                        $session_dt = $this->session->select(array(
                            "user_id" => $user_id
                        ), "try_login", 1);

                        if (!is_null($session_dt)) {

                            $v = c($session_dt, 0, "try_login");

                            if (!is_null($v)) {

                                if (!is_int($v)) $v = intval($v);

                                $try_login = $v;
                            }
                        }
                        // collect ban data
                        $banned_dt = $this->banned->select(array(
                            "user_id" => $user_id
                        ), ["level", "time"], 1);

                        if (!empty($banned_dt)) {

                            $level = c($banned_dt, 0, "level");
                            $timestamp = c($banned_dt, 0, "time");
                            // get level time
                            if (!is_null($level) and !is_null($timestamp)) {

                                if (!is_int($level)) $level = intval($level);
                                if (!is_int($timestamp)) $timestamp = strtotime($timestamp);

                                if (0 <= $level) {

                                    $start = (new Date())->getTimestamp();
                                    $end = Date::enhance_time_ms($this->level[$level], $timestamp);

                                    if ($start < $end) {

                                        $res->render($this->trace("You are blocked, due to abnormal traffic activity!", mode: MessageType::FAILURE)); // yes u got ban, ha ha ha
                                        break;
                                    }
                                }
                            } else {

                                $res->render($this->trace("Something went wrong on the server!", mode: MessageType::FAILURE));
                                break;
                            }
                        }

                        if ($try_login_session_limit <= $try_login) {

                            if (!empty($banned_dt)) {

                                $this->banned->update(array(
                                    "level" => 0 <= $level ? $level + 1 : 0,
                                    "time" => Date::enhance_time(0, (new Date())->getTimestamp())
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
                                "try_login" => 0
                            ), array(
                                "user_id" => $user_id
                            ));

                            $res->render($this->trace("You are blocked, for exceeding the login limit!", mode: MessageType::FAILURE));
                            break;
                        }
                        // check pass
                        $pass = null;

                        $user_dt = $this->user->select(array(
                            "user_uniq" => $user_uniq,
                            "user_email" => $user_email
                        ), "user_pass", 1, "OR");

                        if (!empty($user_dt)) {

                            $pass = c($user_dt, 0, "user_pass");
                        }

                        if ($user_pass == $pass) {

                            // if same
                            // TODO: will loop until token have unique from other
                            $token = createToken(!empty($user_uniq) ? $user_uniq : "unknown");

                            $res->render($this->trace("success login!", array(
                                "token" => $token
                            )));

                            // delete banned
                            $this->banned->delete(array(
                                "user_id" => $user_id
                            ));

                            // delete session
                            // token session lama hangus
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
                            // merekam semua login dalam bentuk variable try_login
                            // memastikan mendapatkan limit pada user

                            if (0 <= $try_login) {

                                $this->session->update(array(
                                    "try_login" => $try_login + 1,
                                    "token" => "" // delete token
                                ), array(
                                    "user_id" => $user_id
                                ));

                            } else {

                                $ip = $this->server->getClientIP();
                                $userAgent = $this->server->HTTP->getUserAgent();

                                $session_try_login = $this->session->select(array(
                                    "user_ip" => $ip,
                                    "user_agent" => $userAgent,
                                    "token" => "",
                                ), "try_login", 1);

                                if (!is_null($session_try_login)) {

                                    $this->session->update(array(
                                        "user_id" => $user_id,
                                        "user_ip" => $ip,
                                        "user_agent" => $userAgent,
                                        "try_login" => 1 + intval($session_try_login),
                                        "token" => "" // delete token
                                    ), array(
                                        "user_ip" => $ip,
                                        "user_agent" => $userAgent,
                                        "token" => "",
                                    ));

                                } else {

                                    $this->session->insert(array(
                                        "user_id" => $user_id,
                                        "user_ip" => $ip,
                                        "user_agent" => $userAgent,
                                        "try_login" => 1,
                                        "token" => "" // delete token
                                    ));
                                }
                            }

                            $res->render($this->trace("failed login!", mode: MessageType::FAILURE));
                            break;
                        }

                        $attach = false;
                    }
                }
            }
        });
    }
}

session_commit();
