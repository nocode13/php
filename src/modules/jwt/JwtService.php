<?php

namespace App\modules\jwt;

use App\exceptions\Exceptions;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
  public function generate(int $userId, string $email)
  {
    $secret = $_ENV['JWT_SECRET'];

    $accessToken = JWT::encode([
      'userId' => $userId,
      'email' => $email,
      'iat' => time(),
      'exp' => time() + 120,
    ], $secret, 'HS256');

    $refreshToken = JWT::encode([
      'userId' => $userId,
      'email' => $email,
      'iat' => time(),
      'exp' => time() + 180,
    ], $secret, 'HS256');

    return [
      'accessToken' => $accessToken,
      'refreshToken' => $refreshToken,
    ];
  }

  public function verify(): array
  {
    $jwt = $this->getTokenFromHeader();

    if (!$jwt) {
      Exceptions::unauthorized();
    }

    $user = $this->verifyToken($jwt);

    if (!$user || $user === 'expired') {
      Exceptions::unauthorized();
    }

    return $user;
  }

  public function verifyToken(string $jwt): array | bool | string
  {
    $secret = $_ENV['JWT_SECRET'];

    try {
      $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));

      return [
        'id' => $decoded->userId,
        'email' => $decoded->email,
      ];
    } catch (\Throwable $th) {
      if ($th instanceof ExpiredException) {
        return 'expired';
      }

      return false;
    }
  }

  public function getTokenFromHeader()
  {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
      return $matches[1];
    }

    return null;
  }
}
