<?php

namespace App\modules\message;

use App\abstractions\Controller;
use App\modules\jwt\JwtService;

class MessageController implements Controller
{
  public function __construct(private MessageService $messageService, private JwtService $jwtService) {}

  public static function checkPath(): bool
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return preg_match('#^/messages(/|$)#', $path);
  }

  public function run(): void
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    $user = $this->jwtService->verify();

    if (preg_match('#^/messages/(\d+)/?$#', $path, $matches) && $method === 'GET') {
      $chatId = (int)$matches[1];
      $cursor = isset($_GET['cursor']) ? (int)$_GET['cursor'] : null;
      $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

      $this->messageService->findByChatId($user, $chatId, $cursor, $limit);
    }

    if (preg_match('#^/messages/?$#', $path, $matches) && $method === 'POST') {
      $data = json_decode(file_get_contents('php://input'), true);
      $this->messageService->create($user, $data);
    }
  }
}
