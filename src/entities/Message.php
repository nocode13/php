<?php

namespace App\entities;

class Message
{
  public int $id;
  public string $text;
  public string $created_at;
  public string $updated_at;
  public string $user_id;
}
