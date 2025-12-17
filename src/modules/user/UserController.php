<?php

namespace App\modules\user;

use App\modules\user\UserService;
use App\abstractions\Controller;

class UserController implements Controller
{
  private $userService;

  public function __construct()
  {
    $this->userService = new UserService();
  }

  static function checkPath(): bool
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return preg_match('#^/users(/|$)#', $path);
  }

  public function run(): void
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    if (preg_match('#^/users/?$#', $path) && $method === 'GET') {
      $this->userService->findAll();
    }
  }
}
