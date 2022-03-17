<?php namespace models;


use tiny\MySQL;


interface UserStructure
{
    public function drop(): void;
    public function create(): void;
    public function insert(
        string | null $user_photo, // URLs
        string $user_name,  // John, Don
        string $user_uniq,  // john_123
        string $user_age,   // 2020-10-12
        string $user_gender,
        string $user_email,
        string $user_pass,
        string | null $user_phone,
        string | null $user_location,
        string | null $user_description,
    ): bool;
    public function update(
        int $user_id, // where
        string $user_photo, // URLs
        string $user_name,  // John, Don
        string $user_uniq,  // john_123
        string $user_age,   // 2020-10-12
        string $user_gender,
        string $user_email,
        string $user_pass,
        string $user_phone,
        string $user_location,
        string $user_description,
    ): bool;
    public function delete(int $user_id): bool;
    public function select(int $user_id): array | null;
}


class User implements UserStructure
{
    private MySQL $conn;

    public function __construct(MySQL $conn)
    {

        $this->conn = $conn;
    }

    public function drop(): void {

        $this->conn->eval("DROP TABLE IF EXISTS `users`");
    }

    public function create(): void
    {

        $this->conn->eval("
            CREATE TABLE IF NOT EXISTS `users`(
                `user_id` INT AUTO_INCREMENT,
                `user_photo` TEXT,
                `user_name` TEXT NOT NULL,
                `user_uniq` TEXT NOT NULL,
                `user_age` DATE NOT NULL,
                `user_gender` TEXT NOT NULL,
                `user_email` TEXT NOT NULL ,
                `user_pass` TEXT NOT NULL,
                `user_phone` TEXT,
                `user_location` TEXT,
                `user_description` TEXT,
                time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(`user_id`)
            )
        ");
    }

    public function insert(
        string | null $user_photo, // URLs
        string $user_name,  // John, Don
        string $user_uniq,  // john_123
        string $user_age,   // 2020-10-12
        string $user_gender,
        string $user_email,
        string $user_pass,
        string | null $user_phone,
        string | null $user_location,
        string | null $user_description,
    ): bool
    {
        $this->conn->eval("
            INSERT INTO `users`(
                `user_photo`,
                `user_name`,
                `user_uniq`,
                `user_age`,
                `user_gender`,
                `user_email` ,
                `user_pass`,
                `user_phone`,
                `user_location`,
                `user_description`
            ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
        ",
            $user_photo,
            $user_name,
            $user_uniq,
            $user_age,
            $user_gender,
            $user_email,
            $user_pass,
            $user_phone,
            $user_location,
            $user_description
        );

        return $this->conn->has_changed();
    }

    public function update(
        int $user_id, // where
        string | null $user_photo, // URLs
        string | null $user_name,  // John, Don
        string | null $user_uniq,  // john_123
        string | null $user_age,   // 2020-10-12
        string | null $user_gender,
        string | null $user_email,
        string | null $user_pass,
        string | null $user_phone,
        string | null $user_location,
        string | null $user_description,
    ): bool
    {

        $maps = [];
        $names = [];
        $qs = [];

        if (!is_null($user_photo)) {
            $maps[] = $user_photo;
            $names[] = "`user_photo`";
            $qs[] = "?";
        }

        if (!is_null($user_name)) {
            $maps[] = $user_name;
            $names[] = "`user_name`";
            $qs[] = "?";
        }

        if (!is_null($user_uniq)) {
            $maps[] = $user_uniq;
            $names[] = "`user_uniq`";
            $qs[] = "?";

        }

        if (!is_null($user_age)) {
            $maps[] = $user_age;
            $names[] = "`user_age`";
            $qs[] = "?";
        }

        if (!is_null($user_gender)) {
            $maps[] = $user_gender;
            $names[] = "`user_gender`";
            $qs[] = "?";
        }

        if (!is_null($user_email)) {
            $maps[] = $user_email;
            $names[] = "`user_email`";
            $qs[] = "?";
        }

        if (!is_null($user_pass)) {
            $maps[] = $user_pass;
            $names[] = "`user_pass`";
            $qs[] = "?";
        }

        if (!is_null($user_phone)) {
            $maps[] = $user_phone;
            $names[] = "`user_phone`";
            $qs[] = "?";
        }

        if (!is_null($user_location)) {
            $maps[] = $user_location;
            $names[] = "`user_location`";
            $qs[] = "?";
        }

        if (!is_null($user_description)) {
            $maps[] = $user_description;
            $names[] = "`user_description`";
            $qs[] = "?";
        }

        $context = join(",", $names);
        $q = join(",", $qs);

        $maps[] = $user_id;

        $this->conn->eval("UPDATE `users`({$context}) SET ({$q}) WHERE `user_id` LIKE ?", ...$maps);

        return $this->conn->has_changed();
    }

    public function delete(int $user_id): bool {

        $this->conn->eval("DELETE FROM `users` WHERE `user_id` LIKE ?", $user_id);

        return $this->conn->has_changed();
    }

    public function select(int $user_id): array | null {

        $d = $this->conn->eval("SELECT * FROM `users` WHERE `user_id` LIKE ?", $user_id);
        return $d->one();
    }
}