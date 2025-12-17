<?php

namespace App\modules\auth;

use App\abstractions\Controller;

class AuthController implements Controller
{
  private $authService;

  public function __construct()
  {
    $this->authService = new AuthService();
  }

  public static function checkPath(): bool
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return preg_match('#^/auth(/|$)#', $path);
  }

  public function run(): void
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    if (preg_match('#^/auth/sign-up/?$#', $path) && $method === 'POST') {
      $data = json_decode(file_get_contents('php://input'), true);
      $this->authService->singUp($data);
    }
  }
}
