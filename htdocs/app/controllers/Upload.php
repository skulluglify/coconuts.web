<?php namespace controllers;

use models\Session;
use models\User;
use tiny\Controller;
use tiny\ControllerStructure;
use tiny\Date;
use tiny\MessageType;
use tiny\MySQL;
use tiny\Request;
use tiny\Response;
use tiny\Server;
use function tiny\base64_safe_en;
use function tiny\c;
use function tiny\createToken;
use function tiny\getExtFromMime;
use function tiny\p;

class Upload extends Controller implements ControllerStructure
{

    protected MySQL $connect;

    // tables
    protected User $user;
    protected Session $session;

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

        $this->server->route("upload", function (Request $req, Response $res) {

            $this->wait = true;

            $data = $req->json();

            if (!empty($data)) {

                $token = c($data, "user_token");
                $user_photo = c($data, "user_photo");

                $token_verify = !empty($token) ? strlen($token) : 0;
                $token_verify = $token_verify > 0 && !($token_verify % 2);

                $attach = true;
                $success = false;
                while ($attach) {

                    if ($token_verify) {

                        if (!empty($user_photo)) {

                            // get id from token (session table)
                            $user_ids = $this->session->select(array(
                                "token" => $token
                            ), "user_id", 1);

                            if (!empty($user_ids)) {

                                $user_id = c($user_ids, 0, "user_id");

                                // is number and start at 1
                                if (is_int($user_id) and $user_id > 0) {

                                    $name = c($user_photo, "name");
                                    $path = c($user_photo, "full_path");
                                    $mime = c($user_photo, "type");
                                    $tmp_name = c($user_photo, "tmp_name");
                                    $error = c($user_photo, "error");
                                    $size = c($user_photo, "size");

                                    $is_win = str_starts_with(strtoupper(PHP_OS), 'WIN');

                                    if (!empty($name) and
                                        !empty($path) and
                                        !empty($mime) and
                                        !empty($tmp_name) and
                                        is_int($error) and // zero is empty, bad filtering
                                        !empty($size)) {

                                        // check error, limit size, type is blob
                                        if ($error == 0 && $size <= 512000 && $name == "blob" && $path == "blob") {

                                            $ext = getExtFromMime($mime);

                                            if (is_string($ext)) {

                                                // now will work
                                                // $distance = realpath("../../storage/users/photos");

                                                if ($is_win) $distance = p("..\\..\\storage\\users\\photos");
                                                else $distance = p("../../storage/users/photos");

                                                if (!is_dir($distance)) mkdir($distance, recursive: true);

                                                if ($distance) {

                                                    // TIMEOUT
                                                    $limit = 5000;
                                                    // will loop until name have unique from other
                                                    while ($limit > 0) {

                                                        $date = new Date();
                                                        $timestamp = $date->getTimestamp();
                                                        $pub = createToken();

                                                        $key = hash_hmac("sha3-256", $token.$pub, Date::enhance_time(0, $timestamp));
                                                        $path = base64_safe_en(hex2bin($key)).".".$ext;

                                                        if ($is_win) $distance = $distance."\\".$path;
                                                        else $distance = $distance."/".$path;

                                                        if (!file_exists($distance)) {

                                                            $check = move_uploaded_file($tmp_name, $distance);

                                                            if ($check) {

                                                                // store name link into user table
                                                                $check = $this->user->update(array(
                                                                    "user_photo" => $is_win ? "users\\photos\\".$path : "users/photos/".$path
                                                                ), array(
                                                                    "id" => $user_id
                                                                ));

                                                                if ($check) $res->render($this->trace("file upload successfully!", mode: MessageType::SUCCESS));
                                                                $success = true;
                                                                break;
                                                            }

                                                            // If got unique name will be break
                                                            break;
                                                        }

                                                        // decrement try generated unique name
                                                        $limit = $limit - 1;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (!$success) $res->render($this->trace("file upload failed!", mode: MessageType::FAILURE));
                    $attach = false;
                }
            }
        });
    }
}