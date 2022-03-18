<?php

require_once "tiny/init.php";

// try catch run
// models, views, controllers
spl_autoload_register(function(string $mainClasses): void {

    $nameSpaces = explode(
        separator: "\\",
        string: $mainClasses
    );

    $dirs = [
        "models", "views", "controllers"
    ];

    foreach ($dirs as $dir)
    {
        $pathClass = join(
            separator: "/",
            array: [
                __DIR__, $dir, join(
                    separator: ".",
                    array: [
                        end(
                            array: $nameSpaces
                        ), "php"
                    ]
                )
            ]
        );

        if (file_exists($pathClass))
        {
            require_once $pathClass;
            break; // no duplicates
        }
    }
});


require_once "Application.php";