<?php

namespace App\modules\env;

use Dotenv\Dotenv;

class EnvService
{
  public function __construct()
  {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__, 3));
    $dotenv->load();
  }
}
