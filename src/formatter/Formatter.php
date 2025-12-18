<?php

namespace App\formatter;

class Formatter
{
  static function response(mixed $data, int $code = 200)
  {
    http_response_code($code);
    echo json_encode([
      'status' => 'success',
      'data' => $data,
    ]);
    exit;
  }
}
