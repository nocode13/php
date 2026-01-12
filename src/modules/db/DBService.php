<?php

namespace App\modules\db;

use PDO;
use stdClass;

class DBService
{
  public PDO $dbh;

  public function __construct()
  {
    $this->connect();
  }

  private function connect()
  {
    $user = $_ENV['POSTGRES_USER'];
    $password = $_ENV['POSTGRES_PASSWORD'];
    $db = $_ENV['POSTGRES_DB'];
    $port = $_ENV['POSTGRES_PORT'];
    $host = $_ENV['POSTGRES_HOST'];

    $this->dbh = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $password);

    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function query(string $sql, array $params = [], $class = null)
  {
    $sth = $this->dbh->prepare($sql);
    $sth->execute($params);

    if ($class === null) {
      return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    return $sth->fetchAll(PDO::FETCH_CLASS, $class);
  }

  public function migrate()
  {
    $files = scandir(__DIR__ . '/migrations');



    foreach ($files as $file) {
      if (!str_ends_with($file, '.sql')) {
        continue;
      }

      $content = file_get_contents(__DIR__ . '/migrations/' . $file);

      $this->dbh->exec($content);
    }
    exit;
  }
}
