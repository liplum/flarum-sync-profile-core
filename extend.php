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
use Illuminate\Contracts\Events\Dispatcher;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Settings())
        ->serializeToForum('stopAvatarChange', 'liplum-sync-profile.stop_avatar_change', function ($var) {
            return (bool) $var;
        })
        ->serializeToForum('stopBioChange', 'liplum-sync-profile.stop_bio_change', function ($var) {
            return (bool) $var;
        }),

    function (Dispatcher $events) {
        $events->subscribe(Listener\UserUpdatedListener::class);
    },

    new Extend\Locales(__DIR__.'/resources/locale'),
];
