<?php
require_once './vendor/autoload.php';

use App\Application;
use App\Container;
use App\modules\env\EnvService;

$app = new Application(new Container(), new EnvService());

$app->run();
