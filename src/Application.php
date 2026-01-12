<?php

namespace App;

use App\exceptions\Exceptions;
use App\modules\auth\AuthController;
use App\modules\db\DBController;
use App\modules\env\EnvService;
use App\modules\user\UserController;
use App\Container;
use App\modules\chat\ChatController;

$counter = 0;

class Application
{
  public function __construct(private Container $container, private EnvService $envService) {}
  public function run()
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($path === '/') {
      echo '<h1>Welcome</h1>';
      exit;
    }

    header('Content-Type: application/json; charset=utf-8');

    if (UserController::checkPath()) {
      $this->container->get(UserController::class)->run();
    }

    if (AuthController::checkPath()) {
      $this->container->get(AuthController::class)->run();
    }

    if (DBController::checkPath()) {
      $this->container->get(DBController::class)->run();
    }

    if (ChatController::checkPath()) {
      $this->container->get(ChatController::class)->run();
    }

    Exceptions::notFound();
  }
}
