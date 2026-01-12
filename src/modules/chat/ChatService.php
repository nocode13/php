<?php

namespace App\modules\chat;

use App\modules\db\DBService;
use App\entities\Chat;
use App\exceptions\Exceptions;
use App\formatter\Formatter;
use PDOException;

class ChatService
{
  public function __construct(private DBService $dbService) {}

  public function findAll(array $user)
  {
    try {
      $chats = $this->dbService->query(
        'SELECT
          c.id,
          c.name,
          c.updated_at,
          c.created_at,
          COALESCE(
            json_agg(
              json_build_object(
                \'id\', u.id,
                \'email\', u.email,
                \'created_at\', u.created_at,
                \'updated_at\', u.updated_at
              )
            ) FILTER (WHERE u.id IS NOT NULL),
            \'[]\'::json
          ) AS "members"
        FROM chats AS c
        LEFT JOIN chat_users AS cu ON cu.chat_id = c.id
        LEFT JOIN users AS u ON u.id = cu.user_id
        WHERE EXISTS (
          SELECT 1 FROM chat_users WHERE chat_id = c.id AND user_id = :u
        )
        GROUP BY c.id
        ',
        [':u' => $user['id']],
      );

      foreach ($chats as &$chat) {
        $chat['members'] = json_decode($chat['members'], true);
      }

      return Formatter::response($chats);
    } catch (\Throwable $th) {
      var_dump($th);
      Exceptions::undefined();
    }
  }

  public function create(array $user, array $body)
  {
    try {
      $this->dbService->dbh->beginTransaction();

      $chats = $this->dbService->query('INSERT INTO chats (name) VALUES (:n) RETURNING *', [':n' => $body['name']]);
      $chat = $chats[0];

      $this->dbService->query('INSERT INTO chat_users (chat_id, user_id) VALUES (:ch, :u)', [':ch' => $chat->id, ':u' => $user['id']]);

      $this->dbService->dbh->commit();
      Formatter::response($chat);
    } catch (PDOException $th) {
      $this->dbService->dbh->rollBack();
      Exceptions::undefined();
    }
  }

  public function attach(array $user, string $id, array $body)
  {
    try {
      $actorChatUser = $this->dbService->query(
        'SELECT * FROM chat_users WHERE chat_id = :c AND user_id = :u',
        [':c' => $id, ':u' => $user['id']]
      );

      if (!sizeof($actorChatUser)) {
        Exceptions::forbidden();
      }

      $chatUser = $this->dbService->query(
        'SELECT * FROM chat_users WHERE chat_id = :c AND user_id = :u',
        [':c' => $id, ':u' => $body['user_id']]
      );

      if (sizeof($chatUser)) {
        Exceptions::custom(400, 'User is already present in this chat');
      }

      $this->dbService->query('INSERT INTO chat_users (chat_id, user_id) VALUES (:c, :u)', [':c' => $id, ':u' => $body['user_id']]);
      Formatter::response([]);
    } catch (PDOException $th) {
      $code = $th->getCode();
      $code === "23503" ? Exceptions::notFound("User or chat not found") : Exceptions::undefined();
    }
  }
}
