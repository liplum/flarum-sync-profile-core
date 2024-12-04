<?php

/*
 * This file is part of askvortsov/flarum-auth-sync
 *
 *  Copyright (c) 2020 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Askvortsov\FlarumAuthSync\Listener;

use Askvortsov\FlarumAuthSync\Models\AuthSyncEvent;
use Flarum\Api\Event\Serializing;
use Flarum\Extension\ExtensionManager;
use Flarum\Group\Group;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\AvatarUploader;
use Flarum\User\Event\GroupsChanged;
use Flarum\User\Event\LoggedIn;
use Flarum\User\Event\Registered;
use Flarum\User\User;
use FoF\Masquerade\Api\Controllers\UserConfigureController;
use FoF\Masquerade\Field;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Intervention\Image\ImageManager;
use Laminas\Diactoros\ServerRequest;

class UserUpdatedListener
{
    protected $avatarUploader;
    protected $extensions;
    protected $settings;

    public function __construct(Container $container, AvatarUploader $avatarUploader, ExtensionManager $extensions, SettingsRepositoryInterface $settings)
    {
        $this->avatarUploader = $avatarUploader;
        $this->extensions = $extensions;
        $this->settings = $settings;
        $this->container = $container;
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(Registered::class, [$this, 'syncRegistered']);
        $events->listen(LoggedIn::class, [$this, 'syncLoggedIn']);
        $events->listen(Serializing::class, [$this, 'syncWorkAround']);
    }

    public function syncRegistered(Registered $event)
    {
        return $this->sync($event->user);
    }

    public function syncLoggedIn(LoggedIn $event)
    {
        return $this->sync($event->user);
    }

    public function syncWorkAround(Serializing $event)
    {
        if (is_array($event->model) && !isset($event->model['on_bio']) && !isset($event->model['validation'])) {
            $user = $event->actor;
            if ($user != null) {
                return $this->sync($user);
            }
        }
    }

    public function sync(User $user)
    {
        $events = AuthSyncEvent::where('email', $user->email)->orderBy('time', 'asc')->get();
        foreach ($events as $event) {
            $attributes = json_decode($event->attributes, true);
            // If Avatar present and avatar sync enabled
            if (isset($attributes['avatar']) && $this->settings->get('askvortsov-auth-sync.sync_avatar', false) && !fnmatch($this->settings->get('askvortsov-auth-sync.ignored_avatar', ''), $attributes['avatar'])) {
                $image = (new ImageManager())->make($attributes['avatar']);
                $this->avatarUploader->upload($user, $image);
            }
            // If group present and group sync enabled
            if (isset($attributes['groups']) && $this->settings->get('askvortsov-auth-sync.sync_groups', false)) {
                $newGroupIds = [];
                foreach ($attributes['groups'] as $group) {
                    if (filter_var($group, FILTER_VALIDATE_INT) && Group::where('id', intval($group))->exists()) {
                        $newGroupIds[] = intval($group);
                    }
                }

                $user->raise(
                    new GroupsChanged($user, $user->groups()->get()->all())
                );

                $user->afterSave(function (User $user) use ($newGroupIds) {
                    $user->groups()->sync($newGroupIds);
                });
            }
            // If bio present and bio sync enabled
            if (isset($attributes['bio']) && $this->settings->get('askvortsov-auth-sync.sync_bio', false)) {
                if ($this->extensions->isEnabled('fof-user-bio') && is_string($attributes['bio'])) {
                    $user->bio = $attributes['bio'];
                }
            }
            // If masquerade present and masquerade sync enabled
            if (isset($attributes['masquerade_attributes']) && $this->settings->get('askvortsov-auth-sync.sync_masquerade', false)) {
                if ($this->extensions->isEnabled('fof-masquerade') && is_array($attributes['masquerade_attributes'])) {
                    $controller = UserConfigureController::class;
                    if (is_string($controller)) {
                        $controller = $this->container->make($controller);
                    }

                    $fields = Field::all();

                    $updatedFields = [];
                    foreach ($fields as $field) {
                        if (isset($attributes['masquerade_attributes'][$field->name])) {
                            $updatedFields[$field->id] = $attributes['masquerade_attributes'][$field->name];
                        }
                    }

                    try {
                        $post_req = new ServerRequest([], [], '/masquerade/configure', 'POST', json_encode($updatedFields));
                        $post_req = $post_req
                            ->withHeader('Content-Type', 'application/json')
                            ->withParsedBody($updatedFields);
                        $post_req = $post_req->withAttribute('bypassCsrfToken', true)->withAttribute('actor', $user);
                        $controller->handle($post_req);
                    } catch (\Exception $e) {
                    }
                }
            }
            $user->save();
            $event->delete();
        }
    }
}
