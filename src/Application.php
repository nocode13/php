<?php

namespace App;

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

    if (UserController::checkPath($path)) {
      new UserController()->run($path, $method);
    }

    if (AuthController::checkPath($path)) {
      new AuthController()->run($path, $method);
    }


    http_response_code(404);
    echo '<h1>Not found</h1>';
  }
}
