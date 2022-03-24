<?php namespace controllers;


use models\Session;
use models\User;
use tiny\Date;
use tiny\MySQL;
use tiny\Server;
use tiny\StringMap;
use function tiny\c;

class Registry
{

    protected MySQL $connect;

    // tables
    protected User $user;
    protected Session $session;

    // server
    protected Server $server;

    // running
    protected bool $wait = false;

    // multiply generic params, levels
    public function __construct(MySQL $conn, Server $server)
    {

        $this->connect = $conn;
        $this->server = $server;

        $this->init();
    }

    protected function init(): void
    {

        $this->user = new User($this->connect);
        $this->session = new Session($this->connect);

        // create tables if exists, debug drop it
        $this->user->create();
        $this->session->create();

        $data = $this->server->getDataJSON();

        // check data json
        if (is_object($data)) {

            // get commands key
            if (property_exists($data, "registry")) {

                $this->wait = true;

                $regis = $data->registry;
                $user_photo = property_exists($regis, "user_photo") ? $regis->user_photo : null;
                $user_name = property_exists($regis, "user_name") ? $regis->user_name : null;
                $user_uniq = property_exists($regis, "user_uniq") ? $regis->user_uniq : null;
                $user_age = property_exists($regis, "user_age") ? $regis->user_age : null;
                $user_gender = property_exists($regis, "user_gender") ? $regis->user_gender : null;
                $user_email = property_exists($regis, "user_email") ? $regis->user_email : null;
                $user_pass = property_exists($regis, "user_pass") ? $regis->user_pass : null;
                $user_phone = property_exists($regis, "user_phone") ? $regis->user_phone : null;
                $user_location = property_exists($regis, "user_location") ? $regis->user_location : null;
                $user_description = property_exists($regis, "user_description") ? $regis->user_description : null;

                $attach = true;
                while ($attach)
                {

                    if (is_null($user_name)) {

                        $this->errorMessage("var user_name cannot be empty!");
                        break;
                    }

                    if (is_null($user_uniq)) {

                        $this->errorMessage("var user_uniq cannot be empty!");
                        break;
                    }

                    if (is_null($user_age)) {

                        $this->errorMessage("var user_age cannot be empty!");
                        break;
                    }

                    if (is_null($user_gender)) {

                        $this->errorMessage("var user_gender cannot be empty!");
                        break;
                    }

                    if (is_null($user_email)) {

                        $this->errorMessage("var user_email cannot be empty!");
                        break;
                    }

                    if (is_null($user_pass)) {

                        $this->errorMessage("var user_pass cannot be empty!");
                        break;
                    }

                    // no dup
                    $check = $this->user->select(array(
                        "user_uniq" => $user_uniq,
                        "user_email" => $user_email
                    ), [
                        "user_uniq",
                        "user_email"
                    ], 1, "OR");

                    if (!empty($check)) {

                        if (count($check) > 0) {

                            $uniq = c($check[0], "user_uniq");
                            $email = c($check[0], "user_email");

                            if ($user_uniq == $uniq) $this->errorMessage("var user_uniq already used by another user!");
                            else if ($user_email == $email) $this->errorMessage("var user_email already used by another user!");

                            break;
                        }
                    }

                    if ($this->user->insert(array(
                        "user_photo" => $user_photo,
                        "user_name" => $user_name,
                        "user_uniq" => $user_uniq,
                        "user_age" => $user_age,
                        "user_gender" => $user_gender,
                        "user_email" => $user_email,
                        "user_pass" => $user_pass,
                        "user_phone" => $user_phone,
                        "user_location" => $user_location,
                        "user_description" => $user_description
                    ))) {

                        $token = Session::createToken($user_name);

                        $this->successMessage("success insert table!", array(
                            "token" => $token
                        ));

                        $user_id = $this->user->getId(array(
                            "user_uniq" => $user_uniq
                        ));

                        if (is_int($user_id)) {

                            // delete session
                            // wait, if someone login
                            // duplicate user ?
                            // $this->session->delete(array(
                                // "user_id" => $user_id
                            // ));

                            $this->session->insert(array(
                                "user_id" => $user_id,
                                "user_ip" => $this->server->getClientIP(),
                                "user_agent" => $this->server->HTTP->getUserAgent(),
                                "try_login" => 0,
                                "token" => $token,
                            ));
                        }

                        // break;

                    } else {

                        $this->errorMessage("failed insert table!");
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