<?php

namespace App\modules\auth;

use App\entities\User;
use App\exceptions\Exceptions;
use App\formatter\Formatter;
use App\modules\db\DBService;
use App\modules\jwt\JwtService;
use PDOException;

class AuthService
{
  public function __construct(private DBService $dbService, private JwtService $jwtService) {}

  public function login(array $body)
  {
    $users = $this->dbService->query('SELECT * FROM users WHERE email = :e', [':e' => $body['email']], User::class);

    if (!sizeof($users)) {
      Exceptions::invalidCredentials();
    }

    $user = $users[0];

    $verified = password_verify($body['password'], $user->hash);

    if (!$verified) {
      Exceptions::invalidCredentials();
    }

    $tokens = $this->jwtService->generate($user->id, $user->email);

    Formatter::response([
      'tokens' => $tokens,
      'user' => [
        'id' => $user->id,
        'email' => $user->email,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
      ],
    ]);
  }

  public function singUp(array $body)
  {
    $hash = password_hash($body['password'], PASSWORD_DEFAULT);

    try {
      $users = $this->dbService->query('INSERT INTO users (email, hash) VALUES (:e, :h) RETURNING id, email, created_at, updated_at', [':e' => $body['email'], ':h' => $hash], User::class);
      $user = $users[0];
    } catch (PDOException $th) {
      $code = $th->getCode();
      $code === '23505' ? Exceptions::alreadyExists() : Exceptions::undefined();
    }

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

    $tokens = $this->jwtService->generate($refreshTokenState['id'], $refreshTokenState['email']);

    Formatter::response($tokens);
  }
}
