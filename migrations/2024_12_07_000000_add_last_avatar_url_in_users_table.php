<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
  'up' => function (Builder $schema) {
    $schema->table('users', function (Blueprint $table) use ($schema) {
      if (!$schema->hasColumn('users', 'last_avatar_url')) {
        $table->text('last_avatar_url')->nullable();
      }
    });
  },

  'down' => function (Builder $schema) {
    $schema->table('users', function (Blueprint $table) {
      $table->dropColumn('last_avatar_url');
    });
  },
];
