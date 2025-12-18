<?php

namespace App\modules\auth;

use App\exceptions\Exceptions;
use App\formatter\Formatter;
use App\modules\db\DBService;
use App\modules\jwt\JwtService;
use PDO;

class AuthService
{
  private $dbService;
  private $jwtService;

  public function __construct()
  {
    $this->dbService = new DBService();
    $this->jwtService = new JwtService();
  }

  public function login(array $body)
  {
    $stmt = $this->dbService->pdo->prepare('SELECT * FROM users WHERE email = :e');
    $stmt->bindValue(':e', $body['email']);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      Exceptions::invalidCredentials();
    }

    $verified = password_verify($body['password'], $user['hash']);

    if (!$verified) {
      Exceptions::invalidCredentials();
    }

    $tokens = $this->jwtService->generate($user['id'], $user['email']);

    Formatter::response([
      'tokens' => $tokens,
      'user' => array([
        'id' => $user['id'],
        'email' => $user['email'],
      ]),
    ]);
  }

  public function singUp(array $body)
  {
    $hash = password_hash($body['password'], PASSWORD_DEFAULT);

    try {
      $stmt = $this->dbService->pdo->prepare('INSERT INTO users (email, hash) VALUES (:e, :h) RETURNING id, email');
      $stmt->bindValue(':e', $body['email']);
      $stmt->bindValue(':h', $hash);
      $stmt->execute();
    } catch (\Throwable $th) {
      $code = $stmt->errorCode();
      $code === '23505' ? Exceptions::alreadyExists() : Exceptions::undefined();
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $tokens = $this->jwtService->generate($user->id, $user->email);

    Formatter::response([
      'tokens' => $tokens,
      'user' => $user,
    ], 201);
  }

  public function refresh(array $body)
  {
    $accessToken = $this->jwtService->getTokenFromHeader();

    $accessTokenState = $this->jwtService->verifyToken($accessToken);

    if (!$accessTokenState) {
      Exceptions::unauthorized();
    }

    $refreshTokenState = $this->jwtService->verifyToken($body['refreshToken']);

    if (!$refreshTokenState || $refreshTokenState === 'expired') {
      Exceptions::unauthorized();
    }

    $tokens = $this->jwtService->generate($refreshTokenState['id'], $refreshTokenState['id']);

    Formatter::response($tokens);
  }
}
