<?php

namespace SQL;

use SQL\Types\Engine;

class Database
{
  private \PDO $pdo;

  public function __construct(
    Engine $engine,
    string $host,
    int    $port,
    string $name,
    string $user,
    string $pasw,
  ) {
    $engineName = strtolower($engine->name);
    $dsn = "$engineName:host=$host;port=$port;dbname=$name;";

    $this->pdo =  new \PDO($dsn, $user, $pasw, [
      \PDO::ATTR_EMULATE_PREPARES => false,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
    ]);
  }

  /**
   * Execute query with binded parameters, returns an associative array with results
   *
   * @param string $query SQL query phrase
   * @param ?array<string,mixed> $params Query parameters
   *
   * @return array<string,mixed>
   *
   * @throws \PDOException
   */
  public function query(string $query, ?array $params = []): array
  {
    $stmt = $this->pdo->prepare($query);

    foreach ($params as $name => $value) {
      $stmt->bindValue($name, $value);
    }

    $stmt->execute();
    return $stmt->fetchAll();
  }
}
