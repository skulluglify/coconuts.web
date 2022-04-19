<?php

/*
 * Untuk Symlink dan Routing handling
 * Membuat skenario jika terjadi kehilangan file .htaccess
 * Merubah link tujuan ke folder public
 * Membuat skenario jika link yang dirubah tidak sesuai
 * Maka akan dialihkan ke link default
 * Create by Ahmad Asy Syafiq
 * Follow my Github https://github.com/skulluglify
 * */

if (!file_exists("\.htaccess")) {

    if (file_put_contents(".htaccess", "DirectoryIndex public/index.html
DirectorySlash Off
IndexIgnore All

Order Deny,Allow
Allow from All

Options +FollowSymLinks
Options -Indexes

RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-s [OR]
RewriteCond %{REQUEST_FILENAME} !-d [OR]
RewriteCond %{REQUEST_FILENAME} !-f [OR]
RewriteCond %{REQUEST_FILENAME} !-l
RewriteRule ^(?!.*(origin|public))([^.]+)(\.([^.]+)|)$ index.php [NC,L]")) {

        header("Location: public/index.html");

    } else {

        echo "file missing .htaccess!";
        echo "<a href='public/index.html'>open this link for homepage redirect, if not get auto redirect!</a>";
    }

} else
if (!empty($_SERVER["REQUEST_SCHEME"]) &&
    !empty($_SERVER["SERVER_NAME"]) && 
    !empty($_SERVER["SERVER_PORT"]) &&
    !empty($_SERVER["REQUEST_URI"])) {

    // symlink
    $url = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/public".$_SERVER["REQUEST_URI"];

    // open url
    header("Location: ".$url);

} else {

    // default symlink
    header("Location: public/index.html");
}