<?php namespace controllers;


use models\Banned;
use models\Session;
use models\User;
use tiny\MySQL;


class Signin
{

    protected MySQL $connect;
    protected array $level;

    // tables
    protected User $user;
    protected Session $session;
    protected Banned $banned;

    public function __construct(MySQL $conn, array $levels)
    {

        $this->connect = $conn;
        $this->level = $levels;
    }
}