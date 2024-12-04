<?php

/*
 * This file is part of askvortsov/flarum-auth-sync
 *
 *  Copyright (c) 2020 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Askvortsov\FlarumAuthSync;

use Flarum\Extend;
use Illuminate\Contracts\Events\Dispatcher;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Settings())
        ->serializeToForum('stopAvatarChange', 'askvortsov-auth-sync.stop_avatar_change', function ($var) {
            return (bool) $var;
        })
        ->serializeToForum('stopBioChange', 'askvortsov-auth-sync.stop_bio_change', function ($var) {
            return (bool) $var;
        }),

    function (Dispatcher $events) {
        $events->subscribe(Listener\UserUpdatedListener::class);
    },

    new Extend\Locales(__DIR__.'/resources/locale'),
];
