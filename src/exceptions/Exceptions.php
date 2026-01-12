<?php

namespace App\exceptions;

class Exceptions
{
  static function unauthorized()
  {
    http_response_code(401);
    echo json_encode([
      'status' => 'error',
      'message' => 'Invalid or missing authentication token'
    ]);
    exit;
  }

  static function invalidCredentials()
  {
    http_response_code(401);
    echo json_encode([
      'status' => 'error',
      'message' => 'Invalid credentials'
    ]);
    exit;
  }

  static function alreadyExists()
  {
    http_response_code(409);
    echo json_encode([
      'status' => 'error',
      'message' => 'User with this email already exists'
    ]);
    exit;
  }

  static function undefined($message = 'Something went wrong')
  {
    http_response_code(500);
    echo json_encode([
      'status' => 'error',
      'message' => $message
    ]);
    exit;
  }

  static function notFound(string $message = 'Not found')
  {
    http_response_code(404);
    echo json_encode([
      'status' => 'error',
      'message' => $message
    ]);
    exit;
  }

  static function forbidden(string $message = 'You do not have access to this action')
  {
    http_response_code(403);
    echo json_encode([
      'status' => 'error',
      'message' => $message
    ]);
    exit;
  }

  static function custom(int $code, string $message)
  {
    http_response_code($code);
    echo json_encode([
      'status' => 'error',
      'message' => $message,
    ]);
    exit;
  }
}
