<?php

namespace App\modules\db;

use App\abstractions\Controller;

class DBController implements Controller
{
  public function __construct(private DBService $dbService) {}

  public static function checkPath(): bool
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return preg_match('#^/migrate(/|$)#', $path);
  }

  public function run(): void
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    if (preg_match('#^/migrate/?$#', $path) && $method === 'POST') {
      $this->dbService->migrate();
    }
  }
}
