<?php namespace tiny;

// find depth, array multiple
function c(array | null $obj, string ...$params) {

    // burn
    if (empty($obj)) return null;

    $temp = $obj;
    foreach ($params as $key) {
        if (array_key_exists($key, $temp)) {
            $temp = $temp[$key];
        } else {
            return null;
        }
    }

    return $temp;
}

// path simplify
// deprecated cause move into realpath
function p(string $path): string | null
{

    $temp = array();
    // $path = realpath($path);
    if (!str_starts_with($path, "/")) $path = getcwd()."/".$path;
    // simplify path
    $current = explode("/", $path);
    foreach ($current as $t) {

        if ($t == ".") continue;
        if ($t == "..") {

            if (!empty($temp)) array_pop($temp);
            else return null;
        }
        else $temp[] = $t;
    }

    if (!empty($temp)) return  join("/", $temp);

    return null;
}

// utilities

function uri_search_de(string $query): array
{

    $temp = array();
    $query = substr($query, strpos($query, "?") + 1, strlen($query) - 1);

    foreach (explode('&', $query) as $chunk) {

        $param = explode("=", $chunk);
        if ($param) $temp[urldecode(current($param))] = urldecode(end($param));

    }

    return $temp;
}

function base64_safe_en(string $context): string
{
    $temp = base64_encode($context);
    $temp = str_replace("+", "-", $temp);
    $temp = str_replace("/", ".", $temp);
    $temp = str_replace("=", "_", $temp);
    return $temp;
}

function base64_safe_de(string $context): string
{
    $temp = str_replace("-", "+", $context);
    $temp = str_replace(".", "/", $temp);
    $temp = str_replace("_", "=", $temp);
    return base64_decode($temp);
}

function createToken(string $labels = "", string $abbr = "UTC"): string
{

    $timestamp = (new Date(abbr: $abbr))->getTimestamp();
    $key = hash("sha3-256", $labels.$timestamp);
    return hash_hmac("sha3-256", $labels."|".str_shuffle($key)."|".$timestamp, $key);
}

function getExtFromMime(string $mime): string | null
{

    return match ($mime) {
        "audio/aac" => "aac",
        "application/x-abiword" => "abw",
        "application/x-freearc" => "arc",
        "image/avif" => "avif",
        "video/x-msvideo" => "avi",
        "application/vnd.amazon.ebook" => "azw",
        "application/octet-stream" => "bin",
        "image/bmp" => "bmp",
        "application/x-bzip" => "bz",
        "application/x-bzip2" => "bz2",
        "application/x-cdf" => "cda",
        "application/x-csh" => "csh",
        "text/css" => "css",
        "text/csv" => "csv",
        "application/msword" => "doc",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => "docx",
        "application/vnd.ms-fontobject" => "eot",
        "font/eot" => "eot",
        "application/epub+zip" => "epub",
        "application/gzip" => "gz",
        "image/gif" => "gif",
        "text/html" => "html",
        "image/vnd.microsoft.icon" => "ico",
        "text/calendar" => "ics",
        "application/java-archive" => "jar",
        "image/jpeg" => "jpg",
        "text/javascript" => "js",
        "application/x-font-ttf" => "ttf",
        "application/x-font-opentype" => "otf",
        "application/x-font-truetype" => "ttf",
        "application/json" => "json",
        "application/ld+json" => "jsonld",
        "audio/midi audio/x-midi" => "midi",
        "audio/mpeg" => "mp3",
        "video/mp4" => "mp4",
        "video/mpeg" => "mpeg",
        "application/vnd.apple.installer+xml" => "mpkg",
        "application/vnd.oasis.opendocument.presentation" => "odp",
        "application/vnd.oasis.opendocument.spreadsheet" => "ods",
        "application/vnd.oasis.opendocument.text" => "odt",
        "audio/ogg" => "oga",
        "video/ogg" => "ogv",
        "application/ogg" => "ogx",
        "audio/opus" => "opus",
        "font/otf" => "otf",
        "font/opentype" => "otf",
        "image/png" => "png",
        "application/pdf" => "pdf",
        "application/x-httpd-php" => "php",
        "application/vnd.ms-powerpoint" => "ppt",
        "application/vnd.openxmlformats-officedocument.presentationml.presentation" => "pptx",
        "application/vnd.rar" => "rar",
        "application/rtf" => "rtf",
        "application/x-sh" => "sh",
        "image/svg+xml" => "svg",
        "application/x-shockwave-flash" => "swf",
        "application/x-tar" => "tar",
        "image/tiff" => "tiff",
        "video/mp2t" => "ts",
        "font/ttf" => "ttf",
        "text/plain" => "txt",
        "application/vnd.visio" => "vsd",
        "audio/wav" => "wav",
        "audio/webm" => "weba",
        "video/webm" => "webm",
        "image/webp" => "webp",
        "font/woff" => "woff",
        "font/woff2" => "woff2",
        "application/xhtml+xml" => "xhtml",
        "application/vnd.ms-excel" => "xls",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => "xlsx",
        "application/xml" => "xml",
        "text/xml" => "xml",
        "application/atom+xml" => "xml",
        "application/vnd.mozilla.xul+xml" => "xul",
        "application/zip" => "zip",
        "video/3gpp" => "3gp",
        "audio/3gpp" => "3gp",
        "video/3gpp2" => "3g2",
        "audio/3gpp2" => "3g2",
        "application/x-7z-compressed" => "7z",
        "application/gzip" => "gzip",
        default => null
    };
}