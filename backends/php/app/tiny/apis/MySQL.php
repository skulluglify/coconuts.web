<?php

namespace tiny;

use mysqli;
//use mysqli_stmt;
use mysqli_result;


interface MySQLStructure
{
    public function has_changed(): bool;
    public function eval(string $query, mixed ...$params): MySQLFetchStructure | bool | null;
    public function close();
}


interface MySQLFetchStructure
{
    public function all(): array | null;
    public function many(int $size): array | null;
    public function one(): array | null;
}


class MySQLFetch implements MySQLFetchStructure
{

    private mysqli_result $result;

    public function __construct(mysqli_result $result)
    {

        $this->result = $result;
    }

    public function all(): array | null
    {

        $result = $this->result->fetch_all();
        if ($result) return $result;
        return null;
    }

    public function many(int $size): array | null
    {

        $result = array();
        while ($row = $this->result->fetch_row())
        {
            if ($size != 0)
            {
                $result[] = $row;
            } else
            {
                break;
            }
            $size = $size - 1;
        }
        if ($result) return $result;
        return null;
    }

    public function one(): array | null
    {

        $result = $this->result->fetch_row();
        if ($result) return $result;
        return null;
    }
}


class MySQL implements MySQLStructure
{
    private array $config;
    private mysqli $cnx;


    public function __construct(string $configname, string $prefix, string $extensions = "ini")
    {

        mysqli_report(flags: MYSQLI_REPORT_OFF);

        if ($prefix) {
            if (strlen($prefix) > 0) {
                $configname = join(separator: "/", array: [
                    $prefix, $configname
                ]);
            }
        }

        $this->config = parse_ini_file(
            filename: join(".", [
                $configname, $extensions
            ]),
            process_sections: true,
            scanner_mode: INI_SCANNER_RAW
        );

        $this->cnx = new mysqli(
            hostname: c($this->config, "mysql", "host"),
            username: c($this->config, "mysql", "user"),
            password: c($this->config, "mysql", "pass"),
            database: c($this->config, "mysql", "name"),
            port: c($this->config, "mysql", "port"),
            socket: c($this->config, "mysql", "sock"),
        );

        if ($this->cnx->connect_errno)
        {

            die("Connection Error: ".$this->cnx->connect_error);
        }
    }

    public function has_changed(): bool {

        return $this->cnx->affected_rows > 0;
    }

    public function eval(string $query, mixed ...$params): MySQLFetchStructure | bool | null
    {

        $types = "";
        // check params is not null
        if (count($params))
        {

            $stmt = $this->cnx->prepare($query);

            foreach ($params as $param)
            {

                $t = gettype($param);
                if (in_array($t, [
                    "integer",
                    "boolean",
                    "NULL"
                ])) {
                    $types .= "i";
                    continue;
                }
                if (in_array($t, [
                    "float",
                    "double"
                ])) {
                    $types .= "d";
                    continue;
                }
                if (in_array($t, [
                    "string"
                ])) {
                    $types .= "s";
                    continue;
                }
                if (in_array($t, [
                    "array"
                ])) {
                    $types .= "b";
                    continue;
                }

                // finally catch
                die("Var can't identify!");
            }

            // Array as Blob
            // Need Handler

            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();

        } else
        {

            $result = $this->cnx->query(query: $query, result_mode: MYSQLI_STORE_RESULT);

            if (is_bool($result)) {
                return $result;
            }

            if ($result instanceof mysqli_result) {

                return new MySQLFetch($result);
            }

            return null;
        }

        return null;
    }

    public function close()
    {

        $this->cnx->commit();
        $this->cnx->close();
    }
}