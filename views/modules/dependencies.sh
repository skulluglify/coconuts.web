#!/usr/bin/env bash


function wload() {

    wget -c $1 -O $2
}

## jquery
{
    mkdir -p jquery
    wload https://unpkg.com/jquery@latest/dist/jquery.slim.min.js jquery/jquery.slim.min.js
}

## bootstrap
{
    mkdir -p bootstrap
    wload https://unpkg.com/bootstrap@latest/dist/js/bootstrap.min.js bootstrap/bootstrap.min.js
    wload https://unpkg.com/bootstrap@latest/dist/js/bootstrap.min.js.map bootstrap/bootstrap.min.js.map
    wload https://unpkg.com/bootstrap@latest/dist/css/bootstrap.min.css bootstrap/bootstrap.min.css
    wload https://unpkg.com/bootstrap@latest/dist/css/bootstrap.min.css.map bootstrap/bootstrap.min.css.map
}

## bootstrap-icons
{
    mkdir -p bootstrap-icons/fonts
    wload https://unpkg.com/bootstrap-icons@latest/font/bootstrap-icons.css bootstrap-icons/bootstrap-icons.css
    wload https://unpkg.com/bootstrap-icons@latest/font/bootstrap-icons.json bootstrap-icons/bootstrap-icons.json
    wload https://unpkg.com/bootstrap-icons@latest/font/fonts/bootstrap-icons.woff bootstrap-icons/fonts/bootstrap-icons.woff
    wload https://unpkg.com/bootstrap-icons@latest/font/fonts/bootstrap-icons.woff2 bootstrap-icons/fonts/bootstrap-icons.woff2
}

## popperjs
{
    mkdir -p popperjs
    wload https://unpkg.com/@popperjs/core@latest/dist/umd/popper.min.js popperjs/popper.min.js
}

## sweetalert2
{
    mkdir -p sweetalert2
    wload https://unpkg.com/sweetalert2@latest/dist/sweetalert2.min.css sweetalert2/sweetalert2.min.js
    wload https://unpkg.com/sweetalert2@latest/dist/sweetalert2.min.css sweetalert2/sweetalert2.min.css
}

## animate
{
    mkdir -p animate
    wload https://unpkg.com/animate.css@latest/animate.min.css animate/animate.min.css
}

## easymde
{
    mkdir -p easymde
    wload https://unpkg.com/easymde@latest/dist/easymde.min.js easymde/easymde.min.js
    wload https://unpkg.com/easymde@latest/dist/easymde.min.css easymde/easymde.min.css
}

## marked
{
    mkdir -p marked
    wload https://unpkg.com/marked@latest/marked.min.js marked/marked.min.js
}
