<?php

require_once "Controller.php";

spl_autoload_register(function(string $mainClasses): void {
    $nameSpaces = explode("\\", $mainClasses);
    $pathClass = join("/", [
        __DIR__,
        "tiny",
        join(".", [
            end(
                $nameSpaces
            ),
            "php"
        ])
    ]);
    if (file_exists($pathClass)) {
        require_once $pathClass;
    }
});