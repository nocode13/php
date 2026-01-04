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

  public function migrate()
  {
    $files = scandir(__DIR__ . '/migrations');



    foreach ($files as $file) {
      if (!str_ends_with($file, '.sql')) {
        continue;
      }

      $content = file_get_contents(__DIR__ . '/migrations/' . $file);

      $this->pdo->exec($content);
    }
    exit;
  }
}
