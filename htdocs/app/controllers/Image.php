<?php namespace controllers;

use tiny\Controller;
use tiny\ControllerStructure;
use tiny\MessageType;
use tiny\Image as ImageLoad;
use tiny\MySQL;
use tiny\Request;
use tiny\Response;
use tiny\Server;
use function tiny\c;
use function tiny\p;

class Image extends Controller implements ControllerStructure
{

    protected MySQL $connect;

    public function __construct(MySQL $conn, Server $server)
    {
        parent::__construct($server);
        $this->connect = $conn;
        $this->init();
    }

    protected function init(): void
    {
        $this->server->route("image", function (Request $req, Response $res) {

            $this->wait = true;
            $data = $req->json();

            if (!empty($data)) {

                $src = c($data, "src");
                $scale = c($data, "scale");

                $attach = true;
                while ($attach) {

                    if (!empty($src)) {

                        $src = str_replace("\/", "/", $src);

                        $path = join("/", [
                            "../../storage",
                            $src
                        ]);

                        $path = p($path);

                        $img = new ImageLoad($path);

                        $mime = $img->getMime();

                        if ($mime != "unknown") {

                             $res->header("Content-Type: $mime");

                             if (!empty($scale)) {

                                 $scale = match ($scale) {
                                     "1" => 48,
                                     "2" => 64,
                                     "3" => 128,
                                     "4" => 140,
                                 };

                                 $img->scaleImage($scale, $scale);
                             }

                             $img->showImage();
                            break;
                        }
                    }

                    // tidak butuh pesan error
                    // karena ini resource handler
                    // $res->render($this->trace("", mode: MessageType::FAILURE));

                    $attach = false;
                }
            }

            $res->header("HTTP/2.0 404 Not Found");
        });
    }
}