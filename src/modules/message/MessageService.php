<?php

namespace App\modules\message;

use App\exceptions\Exceptions;
use App\formatter\Formatter;
use App\modules\db\DBService;

class MessageService
{
  public function __construct(private DBService $dbService) {}

  public function findByChatId(array $user, int $id, ?int $cursor, int $limit)
  {
    try {
      $chatUser = $this->dbService->query(
        'SELECT * FROM chat_users WHERE chat_id = :c AND user_id = :u',
        [
          ':c' => $id,
          ':u' => $user['id']
        ]
      );

      if (!sizeof($chatUser)) {
        Exceptions::forbidden();
      }

      $sql = "
        SELECT
          m.id,
          m.text,
          m.created_at,
          m.updated_at,
          jsonb_build_object(
            'id', u.id,
            'email', u.email,
            'created_at', u.created_at,
            'updated_at', u.updated_at
          ) as \"user\"
        FROM messages AS m
        LEFT JOIN users AS u ON u.id = m.user_id
        WHERE m.chat_id = :chat_id
      ";

      $params = [':chat_id' => $id, ':limit' => $limit];

      if ($cursor) {
        $sql .= " AND m.id < :cursor";
        $params[':cursor'] = $cursor;
      }

      $sql .= " ORDER BY m.id DESC LIMIT :limit";

      $messages = $this->dbService->query($sql, $params);

      foreach ($messages as &$message) {
        $message['user'] = json_decode($message['user'], true);
      }

      $nextCursor = null;
      if (count($messages) === $limit) {
        $lastMessage = end($messages);
        $nextCursor = $lastMessage['id'];
      }

      return Formatter::response([
        'messages' => $messages,
        'next_cursor' => $nextCursor,
      ]);
    } catch (\Throwable $th) {
      var_dump($th);
      Exceptions::undefined();
    }
  }

  public function create(array $user, array $body)
  {
    try {
      $chatUsers = $this->dbService->query(
        'SELECT * FROM chat_users WHERE chat_id = :c AND user_id = :u',
        [
          ':c' => $body['chat_id'],
          ':u' => $user['id']
        ]
      );

      if (!sizeof($chatUsers)) {
        Exceptions::forbidden();
      }

      $messages = $this->dbService->query(
        'INSERT INTO messages (chat_id, user_id, text) VALUES (:c, :u, :t)
        RETURNING
          id,
          text,
          created_at,
          updated_at,
          (
            SELECT jsonb_build_object(
              \'id\', u.id,
              \'email\', u.email,
              \'created_at\', u.created_at,
              \'updated_at\', u.updated_at
            )
            FROM users AS u WHERE u.id = :u
          ) as user
        ',
        [
          ':c' => $body['chat_id'],
          ':u' => $user['id'],
          ':t' => $body['text']
        ],
      );

      $message = $messages[0];

      $message['user'] = json_decode($message['user'], true);

      Formatter::response($message);
    } catch (\Throwable $th) {
      Exceptions::undefined();
    }
  }
}
