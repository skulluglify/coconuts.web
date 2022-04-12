<?php namespace tiny;


use Exception;

interface HTTPCollectionStructure
{
    public function getContentLength(): int;
    public function getContentType(): string | null;
    public function getCookie(): string | null;
    public function getAcceptLanguage(): string | null;
    public function getAcceptEncoding(): string | null;
    public function getReferer(): string | null;
    public function getFetchDest(): string | null;
    public function getFetchUser(): string | null;
    public function getFetchMode(): string | null;
    public function getFetchSite(): string | null;
    public function getUserAgent(): string | null;
    public function getUpgradeInsecureRequest(): int;
    public function getCacheControl(): string | null;
    public function getClientPlatform(): string | null;
    public function isMobile(): bool;
    public function getHost(): string | null;
    public function getClientIP(): string | null; // include HTTP_X_FORWARDED_FOR
}


interface ContentCollectionStructure
{
    public function getType(): string | null;
    public function getLength(): int; // default 0
}


interface URLCollectionStructure
{
    public function getQuery(): string | null;
    public function getURI(): string;
}


interface ResponseCollectionStructure
{
    public function getRedirectStatus(): int;
    public function getServerProtocol(): string;
    public function getGatewayInterface(): string;
}


interface ServerCollectionStructure
{
    public function getPort(): int;
    public function getAddress(): string;
    public function getName(): string;
}


interface RemoteCollectionStructure
{
    public function getPort(): int;
    public function getAddress(): string;
}


interface RequestCollectionStructure
{
    public function getURI(): string;
    public function getMethod(): string;
    public function getTime(): int;
}


interface RequestStructure
{
    public function json(): array | null;
}


interface ResponseStructure
{
    public function header(string $headers): void;
    public function render(mixed $content): void;
}


interface ServerStructure
{
    public function getClientIP(): string;
    public function modify(array $options): void;
    public function route(string $paths, callable $callback): void;
}


class HTTPCollections implements HTTPCollectionStructure
{

    // HTTP Collections
    private string | null $HTTP_CONTENT_LENGTH;
    private string | null $HTTP_CONTENT_TYPE;
    private string | null $HTTP_COOKIE;
    private string | null $HTTP_ACCEPT_LANGUAGE;
    private string | null $HTTP_ACCEPT_ENCODING;
    private string | null $HTTP_REFERER;
    private string | null $HTTP_SEC_FETCH_DEST;
    private string | null $HTTP_SEC_FETCH_USER;
    private string | null $HTTP_SEC_FETCH_MODE;
    private string | null $HTTP_SEC_FETCH_SITE;
    private string | null $HTTP_USER_AGENT;
    private string | null $HTTP_UPGRADE_INSECURE_REQUESTS;
    private string | null $HTTP_CACHE_CONTROL;
    private string | null $HTTP_SEC_CH_UA_PLATFORM;
    private string | null $HTTP_SEC_CH_UA_MOBILE;
    private string | null $HTTP_HOST;
    private string | null $HTTP_CLIENT_IP;
    private string | null $HTTP_X_FORWARDED_FOR;

    public function __construct(array $Collections)
    {

        $this->HTTP_CONTENT_LENGTH = c($Collections, "HTTP_CONTENT_LENGTH");
        $this->HTTP_CONTENT_TYPE = c($Collections, "HTTP_CONTENT_TYPE");
        $this->HTTP_COOKIE = c($Collections, "HTTP_COOKIE");
        $this->HTTP_ACCEPT_LANGUAGE = c($Collections, "HTTP_ACCEPT_LANGUAGE");
        $this->HTTP_ACCEPT_ENCODING = c($Collections, "HTTP_ACCEPT_ENCODING");
        $this->HTTP_REFERER = c($Collections, "HTTP_REFERER");
        $this->HTTP_SEC_FETCH_DEST = c($Collections, "HTTP_SEC_FETCH_DEST");
        $this->HTTP_SEC_FETCH_USER = c($Collections, "HTTP_SEC_FETCH_USER");
        $this->HTTP_SEC_FETCH_MODE = c($Collections, "HTTP_SEC_FETCH_MODE");
        $this->HTTP_SEC_FETCH_SITE = c($Collections, "HTTP_SEC_FETCH_SITE");
        $this->HTTP_USER_AGENT = c($Collections, "HTTP_USER_AGENT");
        $this->HTTP_UPGRADE_INSECURE_REQUESTS = c($Collections, "HTTP_UPGRADE_INSECURE_REQUESTS");
        $this->HTTP_CACHE_CONTROL = c($Collections, "HTTP_CACHE_CONTROL");
        $this->HTTP_SEC_CH_UA_PLATFORM = c($Collections, "HTTP_SEC_CH_UA_PLATFORM");
        $this->HTTP_SEC_CH_UA_MOBILE = c($Collections, "HTTP_SEC_CH_UA_MOBILE");
        $this->HTTP_HOST = c($Collections, "HTTP_HOST");
        $this->HTTP_CLIENT_IP = c($Collections, "HTTP_CLIENT_IP");
        $this->HTTP_X_FORWARDED_FOR = c($Collections, "HTTP_X_FORWARDED_FOR");
    }

    public function getContentLength(): int
    {
        if (!empty($this->HTTP_CONTENT_LENGTH)) {

            return intval($this->HTTP_CONTENT_LENGTH);
        }
        return 0;
    }

    public function getContentType(): string | null
    {
        if (!empty($this->HTTP_CONTENT_TYPE)) {

            return $this->HTTP_CONTENT_TYPE;
        }

        return null;
    }

    public function getCookie(): string | null
    {
        return $this->HTTP_COOKIE;
    }

    public function getAcceptLanguage(): string | null
    {
        return $this->HTTP_ACCEPT_LANGUAGE;
    }

    public function getAcceptEncoding(): string | null
    {
        return $this->HTTP_ACCEPT_ENCODING;
    }

    public function getReferer(): string | null
    {
        return $this->HTTP_REFERER;
    }

    public function getFetchDest(): string | null
    {
        return $this->HTTP_SEC_FETCH_DEST;
    }

    public function getFetchUser(): string | null
    {
        return $this->HTTP_SEC_FETCH_USER;
    }

    public function getFetchMode(): string | null
    {
        return $this->HTTP_SEC_FETCH_MODE;
    }

    public function getFetchSite(): string | null
    {
        return $this->HTTP_SEC_FETCH_SITE;
    }

    public function getUserAgent(): string | null
    {
        return $this->HTTP_USER_AGENT;
    }

    public function getUpgradeInsecureRequest(): int
    {
        if (!empty($this->HTTP_UPGRADE_INSECURE_REQUESTS)) {

            return intval($this->HTTP_UPGRADE_INSECURE_REQUESTS);
        }
        return 0;
    }

    public function getCacheControl(): string | null
    {
        return $this->HTTP_CACHE_CONTROL;
    }

    public function getClientPlatform(): string | null
    {
        return $this->HTTP_SEC_CH_UA_PLATFORM;
    }

    public function isMobile(): bool
    {
        if (!empty($this->HTTP_SEC_CH_UA_MOBILE)) {

            return match ($this->HTTP_SEC_CH_UA_MOBILE) {
                "?1" => true,
                default => false
            };
        }
        return false;
    }

    public function getHost(): string | null
    {
        return $this->HTTP_HOST;
    }

    public function getClientIP(): string | null
    {

        if (!empty($this->HTTP_X_FORWARDED_FOR)) {
        // if (!empty($this->HTTP_X_FORWARDED_FOR)) {

            $forwarded = explode(",", $this->HTTP_X_FORWARDED_FOR);
            $result = current($forwarded); // maybe false
            if ($result) return rtrim($result);
        }

        // another option
        if (!empty($this->HTTP_CLIENT_IP)) {

            return $this->HTTP_CLIENT_IP;
        }

        return null; // must be null, because allocated for remote
    }
}


class ContentCollections implements ContentCollectionStructure
{

    // Content Collections
    private string | null $CONTENT_TYPE;
    private string | null $CONTENT_LENGTH;

    public function __construct(array $Collections)
    {

        $this->CONTENT_TYPE = c($Collections, "CONTENT_TYPE");
        $this->CONTENT_LENGTH = c($Collections, "CONTENT_LENGTH");
    }

    public function getType(): string | null
    {
        return $this->CONTENT_TYPE;
    }

    public function getLength(): int
    {
        if (!empty($this->CONTENT_LENGTH)) {

            return intval($this->CONTENT_LENGTH);
        }
        return 0;
    }
}


class URLCollections implements URLCollectionStructure
{
    // URL Collections
    private string | null $QUERY_STRING;
    private string | null $DOCUMENT_URI;

    public function __construct(array $Collections)
    {

        $this->QUERY_STRING = c($Collections,"QUERY_STRING");
        $this->DOCUMENT_URI = c($Collections,"DOCUMENT_URI");
    }

    public function getQuery(): string | null
    {
        return $this->QUERY_STRING;
    }

    public function getURI(): string
    {
        if (!empty($this->DOCUMENT_URI)) {

            return $this->DOCUMENT_URI;
        }
        return "/";
    }
}


class ResponseCollections implements ResponseCollectionStructure
{
    // Response Collections
    private string | null $REDIRECT_STATUS;
    private string | null $GATEWAY_INTERFACE;
    private string | null $SERVER_PROTOCOL;

    public function __construct(array $Collections)
    {
        $this->REDIRECT_STATUS = c($Collections,"REDIRECT_STATUS");
        $this->GATEWAY_INTERFACE = c($Collections,"GATEWAY_INTERFACE");
        $this->SERVER_PROTOCOL = c($Collections,"SERVER_PROTOCOL");
    }

    public function getRedirectStatus(): int
    {
        if (!empty($this->REDIRECT_STATUS)) {

            return intval($this->REDIRECT_STATUS); // like 200
        }
        return 404; // maybe wrong
    }

    public function getServerProtocol(): string
    {
        if (!empty($this->SERVER_PROTOCOL)) {

            return $this->SERVER_PROTOCOL; // like HTTP/1.1
        }
        return "HTTP/1.1"; // maybe wrong
    }

    public function getGatewayInterface(): string
    {
        if (!empty($this->GATEWAY_INTERFACE)) {

            return $this->GATEWAY_INTERFACE;
        }
        return "CGI/1.1"; // maybe wrong
    }
}


class ServerCollections implements ServerCollectionStructure
{
    // Server Collections
    private string | null $SERVER_PORT;
    private string | null $SERVER_ADDRESS;
    private string | null $SERVER_NAME;

    public function __construct(array $Collections)
    {
        $this->SERVER_PORT = c($Collections,"SERVER_PORT");
        $this->SERVER_ADDRESS = c($Collections,"SERVER_ADDR");
        $this->SERVER_NAME = c($Collections,"SERVER_NAME");
    }

    public function getPort(): int
    {
        if (!empty($this->SERVER_PORT)) {

            return intval($this->SERVER_PORT);
        }
        return 80; // assume is http://
    }

    public function getAddress(): string
    {
        if (!empty($this->SERVER_ADDRESS)) {

            return $this->SERVER_ADDRESS;
        }
        return "0.0.0.0"; // unknown, default
    }

    public function getName(): string
    {
        if (!empty($this->SERVER_NAME)) {

            return $this->SERVER_NAME;
        }
        return "Unknown Server"; // assume is unknown server
    }
}


class RemoteCollections implements RemoteCollectionStructure
{
    // Remote Collections
    private string | null $REMOTE_PORT;
    private string | null $REMOTE_ADDRESS;

    public function __construct(array $Collections)
    {
        $this->REMOTE_PORT = c($Collections,"REMOTE_PORT");
        $this->REMOTE_ADDRESS = c($Collections,"REMOTE_ADDR");
    }


    public function getPort(): int
    {
        if (!empty($this->REMOTE_PORT)) {

            return intval($this->REMOTE_PORT);
        }
        return 50000; // maybe wrong, but close
    }

    public function getAddress(): string
    {
        if (!empty($this->REMOTE_ADDRESS)) {

            return $this->REMOTE_ADDRESS;
        }
        return "127.0.0.1";
    }
}


class RequestCollections implements RequestCollectionStructure
{
    // Request Collections
    private string | null $REQUEST_METHOD;
    private string | null $REQUEST_URI;
    private string | int | null $REQUEST_TIME;

    public function __construct(array $Collections)
    {
        $this->REQUEST_METHOD = c($Collections,"REQUEST_METHOD");
        $this->REQUEST_URI = c($Collections,"REQUEST_URI");
        $this->REQUEST_TIME = c($Collections,"REQUEST_TIME");
    }

    public function getURI(): string
    {
        if (!empty($this->REQUEST_URI)) {

            return $this->REQUEST_URI;
        }
        return "/";
    }

    public function getMethod(): string
    {
        if (!empty($this->REQUEST_METHOD)) {

            return $this->REQUEST_METHOD;
        }
        return "GET";
    }

    public function getTime(): int
    {
        if (!empty($this->REQUEST_TIME)) {

            if (is_int($this->REQUEST_TIME)) {

                return $this->REQUEST_TIME;
            }

            return intval($this->REQUEST_TIME);
        }
        return 0; // start at 0, 1900, Jan, 1
    }
}


class Request implements RequestStructure
{

    // private HTTPCollectionStructure $HTTP;

    public function __construct(/* $http */)
    {

        // $this->HTTP = $http;
        // nothing to do
    }
    public function json() : array | null
    {

        $data = array();

        // headers Content-Type application/json
        if (empty($_POST) or empty($_FILES)) {

            // using content-type, sometimes is empty string
            // want filtering is application/json, but failure using xampp

            $inputs = file_get_contents("php://input");

            if ($inputs and is_string($inputs) and strlen($inputs) > 1) { // [], {}

                try {

                    // Body
                    // JSON_OBJECT_AS_ARRAY set by associative
                    $data = json_decode($inputs, associative: true, flags: JSON_BIGINT_AS_STRING);

                    if (!empty($data)) return $data;

                } catch (Exception) {

                    return null;
                }
            }
        }

        // catch _FILES nad _POST
        if (!empty($_FILES)) $data = array_merge(array(), $_FILES); // copy
        if (!empty($_POST)) $data = array_merge($data, $_POST);
        if (!empty($data)) return $data;

        return null;
    }
}


class Response implements ResponseStructure
{

    // private HTTPCollectionStructure $HTTP; // never used anywhere

    public function __construct(/* $http */)
    {

        // $this->HTTP = $http;
        // nothing to do
    }

    public function header(string $headers) : void
    {

        // replacement in built in
        header($headers);
    }

    public function render(mixed $content) : void
    {
        if (is_array($content)) echo json_encode($content);
        else if (is_string($content)) echo $content;
        else if (is_int($content)) echo $content;
        else echo "<unknown/>";
    }
}


class Server implements ServerStructure
{
    // ROUTING
    // GET POST PUT DELETE PATCH
    protected string $prefix;

    public HTTPCollectionStructure $HTTP;
    public ContentCollectionStructure $Content;
    public URLCollectionStructure $URL;
    public ResponseCollectionStructure $Response;
    public ServerCollectionStructure $Server;
    public RemoteCollectionStructure $Remote;
    public RequestCollectionStructure $Request;

    public function __construct()
    {
        $Collections = $_SERVER;
        $this->prefix = "";

        if (!empty($Collections)) {

            $this->HTTP = new HTTPCollections($Collections);
            $this->Content = new ContentCollections($Collections);
            $this->URL = new URLCollections($Collections);
            $this->Response = new ResponseCollections($Collections);
            $this->Server = new ServerCollections($Collections);
            $this->Remote = new RemoteCollections($Collections);
            $this->Request = new RequestCollections($Collections);
        }

        // set default headers
        header("X-Powered-By: Tiny Service .Ltd");
        header("Access-Control-Allow-Origin: Same-Origin");
        header("Vary: Origin");
    }

    public function getClientIP(): string
    {
        $client_ip = $this->HTTP->getClientIP();
        $remote_address = $this->Remote->getAddress();

        if (!empty($client_ip)) {

            return $client_ip;
        }

        if (!empty($remote_address)) {

            return $remote_address;
        }

        return "127.0.0.1"; // default
    }

    // new concept
    public function modify(array $options): void
    {

        // nothing to do
        // belum ada ide, bruh
        // prefix, suffix uri mungkin
    }

    // new concept
    public function route(string $paths, callable $callback): void {

        $uri = $this->Request->getURI();
        $origin = join("/", [$this->prefix, $paths]);

        if (!str_starts_with($origin, "/")) $origin = "/".$origin;
        if (str_ends_with($uri, "/")) $uri = substr($uri, 0, strlen($uri) - 1);

        // tidak bagus, tapi apa daya untuk tugas akhir
        // takut nya directory app diletakan di sub directory
        // maka tidak akan routing secara betul, bruh ...
        // lebih baik pakai equals operator untuk production build
        if (str_ends_with($uri, $origin))
        {

            $req = new Request(/* $this->HTTP */);
            $res = new Response(/* $this->HTTP */);
            
            if (is_callable($callback)) {

                $res->header("HTTP/2.0 200 OK");
                $callback($req, $res);
            } else {

                $res->header("HTTP/2.0 401 Unauthorized");
            }
        }
    }
}