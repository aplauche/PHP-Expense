<?php

declare(strict_types=1);

namespace Framework;

use PDO, PDOException;

class Database
{

  public PDO $connection;

  public function __construct(string $driver, array $config, string $username, string $password)
  {

    // helper for building query string - in this case we use custom semicolon seperator
    $config = http_build_query(data: $config, arg_separator: ';');

    $dsn = "{$driver}:{$config}";

    // we need to use a custom error to make sure sensitive info does not get dumped
    try {
      $this->connection = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
      die("Error connecting to database.");
    }
  }
}
