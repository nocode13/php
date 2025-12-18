<?php

namespace App\modules\user;

use App\exceptions\Exceptions;
use App\formatter\Formatter;
use App\modules\db\DBService;
use PDO;

class UserService
{
  private $postgres;

  public function __construct()
  {
    $this->postgres = new DBService();
  }

  public function findAll()
  {
    try {
      $stmt = $this->postgres->pdo->query('SELECT id, email FROM users');
      $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

      Formatter::response($users);
    } catch (\Throwable $th) {
      Exceptions::undefined();
    }
  }
}
