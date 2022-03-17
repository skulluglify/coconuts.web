<?php

require_once "core.php";

// apis
spl_autoload_register(function(string $mainClasses): void {
    $nameSpaces = explode(
        separator: "\\",
        string: $mainClasses
    );

    $pathClass = join(
        separator: "/",
        array: [
            __DIR__, "apis", join(
                separator: ".",
                array: [
                    end(
                        array: $nameSpaces
                    ), "php"
                ]
            )
        ]
    );

    if (file_exists($pathClass)) {
        require_once $pathClass;
    }
});