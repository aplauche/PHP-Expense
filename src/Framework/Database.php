<?php

declare(strict_types=1);

namespace Framework;

use PDO, PDOException, PDOStatement;

class Database
{

  public PDO $connection;
  private PDOStatement $stmt;

  public function __construct(string $driver, array $config, string $username, string $password)
  {

    // helper for building query string - in this case we use custom semicolon seperator
    $config = http_build_query(data: $config, arg_separator: ';');

    $dsn = "{$driver}:{$config}";

    // we need to use a custom error to make sure sensitive info does not get dumped
    try {
      $this->connection = new PDO($dsn, $username, $password, [
        // add default mode
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ]);
    } catch (PDOException $e) {
      die("Error connecting to database.");
    }
  }

  public function query(string $query, array $params = []): Database
  {
    $this->stmt = $this->connection->prepare($query);

    $this->stmt->execute($params);

    return $this;
  }

  public function count()
  {
    return $this->stmt->fetchColumn(0);
  }

  public function find()
  {
    return $this->stmt->fetch();
  }

  public function findAll()
  {
    return $this->stmt->fetchAll();
  }

  public function lastId()
  {
    return $this->connection->lastInsertId();
  }
}
