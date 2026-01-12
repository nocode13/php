<?php

namespace App\modules\user;

use App\entities\User;
use App\exceptions\Exceptions;
use App\formatter\Formatter;
use App\modules\db\DBService;

class UserService
{
  public function __construct(private DBService $dbService) {}

  public function findAll()
  {
    try {
      $users = $this->dbService->query('SELECT id, email, created_at, updated_at FROM users', [], User::class);

      Formatter::response($users);
    } catch (\Throwable $th) {
      Exceptions::undefined();
    }
  }
}
