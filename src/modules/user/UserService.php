<?php

namespace App\modules\user;

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
    $stmt = $this->postgres->pdo->query('SELECT * FROM users');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($users);
    exit;
  }
}
