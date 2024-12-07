<?php

namespace Liplum\SyncProfile\Event;

class SyncProfileEvent
{
  public string $email;

  public array $attributes;

  public function __construct(string $email, array $attributes)
  {
    $this->email = $email;
    $this->attributes = $attributes;
  }
}
