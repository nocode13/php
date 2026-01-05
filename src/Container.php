<?php

namespace App;

use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
  public function has(string $id): bool
  {
    return class_exists($id);
  }

  public function get(string $class): object
  {
    $classReflector = new ReflectionClass($class);

    $constructReflector = $classReflector->getConstructor();
    if (empty($constructReflector)) {
      return new $class;
    }

    $constructArguments = $constructReflector->getParameters();
    if (empty($constructArguments)) {
      return new $class;
    }

    $args = [];
    foreach ($constructArguments as $argument) {
      $argumentType = $argument->getType()->getName();
      $args[$argument->getName()] = $this->get($argumentType);
    }

    return new $class(...$args);
  }
}
