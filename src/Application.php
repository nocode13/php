<?php

namespace App;

use App\exceptions\Exceptions;
use App\modules\auth\AuthController;
use App\modules\env\EnvService;
use App\modules\user\UserController;

class Application
{
  public function __construct()
  {
    new EnvService();
  }
  public function run()
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    if ($path === '/') {
      echo '<h1>Welcome</h1>';
      exit;
    }

    header('Content-Type: application/json; charset=utf-8');

    if (UserController::checkPath($path)) {
      new UserController()->run($path, $method);
    }

    if (AuthController::checkPath($path)) {
      new AuthController()->run($path, $method);
    }

    Exceptions::notFound();
  }
}
