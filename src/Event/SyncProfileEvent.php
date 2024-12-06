<?php

namespace Liplum\SyncProfile\Event;

class SyncProfileEvent
{
  public string $email;

  public $attributes;

  public $time;

  public function __construct(string $email, $attributes, $time)
  {
    $this->email = $email;
    $this->attributes = $attributes;
    $this->time = $time;
  }
}
