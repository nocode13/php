<?php

namespace App\entities;

class Chat
{
  public int $id;
  public string $name;
  public string $created_at;
  public string $updated_at;
  public array $members;
}
