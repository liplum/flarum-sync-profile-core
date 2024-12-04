<?php

namespace Liplum\SyncProfile\Models;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;

/**
 * @property string email
 * @property Carbon time
 * @property string attributes
 */
class AuthSyncEvent extends AbstractModel
{
    protected $table = 'liplum_sync_profile_events';
    protected $dates = ['time'];
}
