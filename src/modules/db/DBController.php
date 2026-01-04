<?php

namespace App\modules\db;

use App\abstractions\Controller;
use App\modules\jwt\JwtService;

class DBController implements Controller
{
  private DBService $dbService;
  private JwtService $jwtService;

  public function __construct()
  {
    $this->dbService = new DBService();
    $this->jwtService = new JwtService();
  }

  public static function checkPath(): bool
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return preg_match('#^/migrate(/|$)#', $path);
  }

  public function run(): void
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    $credentials = $this->jwtService->verify();

    if (preg_match('#^/migrate/?$#', $path) && $method === 'POST') {
      $this->dbService->migrate();
    }
  }
}
