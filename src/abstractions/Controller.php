<?php

namespace App\abstractions;

interface Controller
{
  static function checkPath(): bool;
  public function run(): void;
}
