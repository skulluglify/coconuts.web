<?php namespace tiny;

use mysqli;
use mysqli_stmt;
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

        $result = $this->result->fetch_all(mode: MYSQLI_ASSOC);
        if ($result) return $result;
        return null;
    }

    public function many(int $size): array | null
    {

        $result = array();
        while ($row = $this->result->fetch_assoc())
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

        $result = $this->result->fetch_assoc();
        if ($result) return $result;
        return null;
    }
}


class MySQL implements MySQLStructure
{
    private array $config;
    private mysqli $cnx;


    public function __construct(string $config, string $prefix, string $suffix = "ini")
    {

        mysqli_report(flags: MYSQLI_REPORT_OFF);

        if ($prefix) {
            if (strlen($prefix) > 0) {
                $config = join(separator: "/", array: [
                    $prefix, $config
                ]);
            }
        }

        $this->config = parse_ini_file(
            filename: join(".", [
                $config, $suffix
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

        // start at 0, is true
        return 0 <= $this->cnx->affected_rows;
    }

    public function eval(string $query, mixed ...$params): MySQLFetchStructure | bool | null
    {

        $types = "";
        // check params is not null
        if (count($params) > 0)
        {

            $stmt = $this->cnx->prepare($query);

            $result = false;

            if ($stmt instanceof mysqli_stmt) {

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
                    if ($t == "string") {
                        $types .= "s";
                        continue;
                    }

                    // bad idea
                    if ($t == "array") {
                        $types .= "b";

                        // convert to string
                        $params = join("", array_map(function ($codepoint) {

                            return chr($codepoint);
                        }, $params));
                        continue;
                    }

                    // finally catch
                    die("Var can't identify!");
                }

                // Array as Blob
                // Need Handler

                $stmt->bind_param($types, ...$params);

                // if success
                if ($stmt->execute()) {

                    $result = $stmt->get_result();

                    // data has been sending but not have result ...
                    if (is_bool($result)) $result = true;
                }

                $stmt->close();

            }

        } else
        {

            $result = $this->cnx->query(query: $query, result_mode: MYSQLI_STORE_RESULT);

        }

        if (is_bool($result)) return $result;

        if ($result instanceof mysqli_result) {

            return new MySQLFetch($result);
        }

        return null;
    }

    public function close()
    {

        $this->cnx->commit();
        $this->cnx->close();
    }
}
