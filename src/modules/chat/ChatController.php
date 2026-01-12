<?php

namespace App\modules\chat;

use App\abstractions\Controller;
use App\modules\jwt\JwtService;

class ChatController implements Controller
{
  public function __construct(private ChatService $chatService, private JwtService $jwtService) {}

  public static function checkPath(): bool
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return preg_match('#^/chats(/|$)#', $path);
  }

  public function run(): void
  {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    $user = $this->jwtService->verify();

    if (preg_match('#^/chats/?$#', $path) && $method === 'GET') {
      $this->chatService->findAll($user);
    }

    if (preg_match('#^/chats/?$#', $path) && $method === 'POST') {
      $data = json_decode(file_get_contents('php://input'), true);
      $this->chatService->create($user, $data);
    }

    if (preg_match('#^/chats/(\d+)/attach/?$#', $path, $matches) && $method === 'POST') {
      $chatId = (int)$matches[1];
      $data = json_decode(file_get_contents('php://input'), true);
      $this->chatService->attach($user, $chatId, $data);
    }
  }
}
