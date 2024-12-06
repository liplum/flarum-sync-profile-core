<?php

namespace Liplum\SyncProfile\Event;

class SyncProfileEvent
{
  public string $email;

  public $attributes;

  public function __construct(string $email, $attributes)
  {
    $this->email = $email;
    $this->attributes = $attributes;
  }
}
