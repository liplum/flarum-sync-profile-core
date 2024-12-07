<?php

/*
 * This file is part of liplum/flarum-sync-profile
 *
 *  Copyright (c) 2020 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Liplum\SyncProfile;

use Flarum\Extend;
use Liplum\SyncProfile\Listener\SyncProfileEventListener;

return [
  (new Extend\Frontend('admin'))
    ->js(__DIR__ . '/js/dist/admin.js'),

  (new Extend\Frontend('forum'))
    ->js(__DIR__ . '/js/dist/forum.js'),

  (new Extend\Settings())
    ->serializeToForum('liplum-sync-profile-core.block-profile-changes', 'liplum-sync-profile-core.block-profile-changes', function ($var) {
      return (bool) $var;
    }),

  (new Extend\Event)
    ->subscribe(SyncProfileEventListener::class),

  new Extend\Locales(__DIR__ . '/resources/locale'),

  (new Extend\Settings())
    ->default('liplum-sync-profile-core.ignore-unchanged-avatar', true),
];
