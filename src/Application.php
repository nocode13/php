<?php

namespace App;

use App\exceptions\Exceptions;
use App\modules\auth\AuthController;
use App\modules\db\DBController;
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

    if ($path === '/') {
      echo '<h1>Welcome</h1>';
      exit;
    }

    header('Content-Type: application/json; charset=utf-8');

    if (UserController::checkPath()) {
      new UserController()->run();
    }

    if (AuthController::checkPath()) {
      new AuthController()->run();
    }

    if (DBController::checkPath()) {
      new DBController()->run();
    }

    Exceptions::notFound();
  }
}
