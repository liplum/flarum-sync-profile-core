<?php

/*
 * This file is part of askvortsov/flarum-auth-sync
 *
 *  Copyright (c) 2020 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Askvortsov\FlarumAuthSync\Models;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;

/**
 * @property string email
 * @property Carbon time
 * @property string attributes
 */
class AuthSyncEvent extends AbstractModel
{
    protected $table = 'auth_sync_events';
    protected $dates = ['time'];
}
