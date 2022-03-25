<?php namespace controllers;


use models\Session;
use models\User;
use tiny\Controller;
use tiny\ControllerStructure;
use tiny\MessageType;
use tiny\MySQL;
use tiny\Server;
use function tiny\c;

class Registry extends Controller implements ControllerStructure
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

        parent::__construct($server);
        $this->connect = $conn;
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
        if (is_array($data)) {

            // get commands key
            $regis = c($data, "registry");

            if (!empty($regis)) {

                $this->wait = true;

                $user_photo = c($regis, "user_photo");
                $user_name = c($regis, "user_name");
                $user_uniq = c($regis, "user_uniq");
                $user_age = c($regis, "user_age");
                $user_gender = c($regis, "user_gender");
                $user_email = c($regis, "user_email");
                $user_pass = c($regis, "user_pass");
                $user_phone = c($regis, "user_phone");
                $user_location = c($regis, "user_location");
                $user_description = c($regis, "user_description");

                $attach = true;
                while ($attach)
                {

                    if (is_null($user_name)) {

                        $this->setMessage("var user_name cannot be empty!", mode: MessageType::FAILURE);
                        break;
                    }

                    if (is_null($user_uniq)) {

                        $this->setMessage("var user_uniq cannot be empty!", mode: MessageType::FAILURE);
                        break;
                    }

                    if (is_null($user_age)) {

                        $this->setMessage("var user_age cannot be empty!", mode: MessageType::FAILURE);
                        break;
                    }

                    if (is_null($user_gender)) {

                        $this->setMessage("var user_gender cannot be empty!", mode: MessageType::FAILURE);
                        break;
                    }

                    if (is_null($user_email)) {

                        $this->setMessage("var user_email cannot be empty!", mode: MessageType::FAILURE);
                        break;
                    }

                    if (is_null($user_pass)) {

                        $this->setMessage("var user_pass cannot be empty!", mode: MessageType::FAILURE);
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

                            if ($user_uniq == $uniq) $this->setMessage("var user_uniq already used by another user!", mode: MessageType::FAILURE);
                            else if ($user_email == $email) $this->setMessage("var user_email already used by another user!", mode: MessageType::FAILURE);

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

                        $this->setMessage("success insert table!", array(
                            "token" => $token
                        ));

                        $user_id = $this->user->getId(array(
                            "user_uniq" => $user_uniq
                        ));

                        if (is_int($user_id)) {

                            // delete session
                            // wait, if someone logs in
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

                        $this->setMessage("failed insert table!", mode: MessageType::FAILURE);
                        break;
                    }

                    $attach = false;
                }
            }
        }
    }
}