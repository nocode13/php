<?php

namespace App\modules\db;

use PDO;

class DBService
{
  public PDO $pdo;

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

    $this->pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $password);

    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
}
