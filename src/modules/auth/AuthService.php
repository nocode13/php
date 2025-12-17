<?php

namespace App\modules\auth;

use App\modules\db\DBService;
use PDO;

class AuthService
{
  private $postgres;

  public function __construct()
  {
    $this->postgres = new DBService();
  }

  public function singUp(array $body)
  {
    $hash = password_hash($body['password'], PASSWORD_DEFAULT);

    $stmt = $this->postgres->pdo->prepare('INSERT INTO users (email, hash) VALUES (:e, :h) RETURNING id, email');
    $stmt->bindValue(':e', $body['email']);
    $stmt->bindValue(':h', $hash);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');
    http_response_code(201);
    echo json_encode($user);
    exit;
  }
}
