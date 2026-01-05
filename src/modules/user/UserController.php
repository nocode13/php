<?php

namespace App\modules\user;

use App\modules\user\UserService;
use App\abstractions\Controller;
use App\modules\jwt\JwtService;

class UserController implements Controller
{
  public function __construct(private UserService $userService, private JwtService $jwtService) {}

  static function checkPath(): bool
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return preg_match('#^/users(/|$)#', $path);
  }

  public function run(): void
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    $credentials = $this->jwtService->verify();

    if (preg_match('#^/users/?$#', $path) && $method === 'GET') {
      $this->userService->findAll();
    }
  }
}
