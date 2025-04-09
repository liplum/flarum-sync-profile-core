<?php

namespace Liplum\SyncProfile\Listener;

use Liplum\SyncProfile\Event\SyncProfileEvent;

use Flarum\Extension\ExtensionManager;
use Flarum\Group\Group;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\AvatarUploader;
use Flarum\User\Event\GroupsChanged;
use Flarum\User\User;
use FoF\Masquerade\Api\Controllers\UserConfigureController;
use FoF\Masquerade\Field;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Intervention\Image\ImageManager;
use Laminas\Diactoros\ServerRequest;
use Flarum\Foundation\Config;
use Psr\Log\LoggerInterface;

class SyncProfileEventListener
{
  private $avatarUploader;
  private $extensions;
  private $settings;
  private $container;
  private $config;
  private $log;

  public function __construct(
    Container $container,
    AvatarUploader $avatarUploader,
    ExtensionManager $extensions,
    SettingsRepositoryInterface $settings,
    Config $config,
    LoggerInterface $log,
  ) {
    $this->avatarUploader = $avatarUploader;
    $this->extensions = $extensions;
    $this->settings = $settings;
    $this->container = $container;
    $this->config = $config;
    $this->log = $log;
  }

  public function subscribe(Dispatcher $events)
  {
    $events->listen(SyncProfileEvent::class, [$this, 'syncEvent']);
  }

  public function syncEvent(SyncProfileEvent $event)
  {
    $email = $event->email;
    $user = User::query()->where("email", $email)->first();
    if (!$user) {
      $this->log->debug("Failed sync profile, due to $email not found");
      return;
    }
    $this->sync($user, $event->attributes);
    $user->save();
    $this->log->debug("Synced $event->email");
  }

  private function syncNickname(User $user, $attributes)
  {
    // If nickname present and nickname sync enabled
    $nickname = $attributes["nickname"];
    $nickname = is_string($nickname) ? $nickname : null;
    if (
      isset($nickname)
      && $this->settings->get('liplum-sync-profile-core.sync-nickname', false)
      && $nickname != $user->$nickname
    ) {
      if (!$this->extensions->isEnabled('flarum-nicknames')) {
        $this->log->debug("Sync user profile: 'nickname' failed, because extension 'flarum-nicknames' is not enabled.");
      } else {
        $user->nickname = $nickname;
      }
    }
  }

  private function syncBio(User $user, $attributes)
  {
    // If bio present and bio sync enabled
    $bio = $attributes["bio"];
    $bio = is_string($bio) ? $bio : null;
    if (
      $this->settings->get('liplum-sync-profile-core.sync-bio', false)
      && isset($bio)
      && $bio != $user->bio
    ) {
      if (!$this->extensions->isEnabled('fof-user-bio')) {
        $this->log->debug("Sync user profile: 'nickname' failed, because extension 'fof/user-bio' is not enabled.");
      } else {
        $user->bio = $bio;
      }
    }
  }

  private function syncAvatar(User $user, $attributes)
  {
    // If avatar present and avatar sync enabled
    $avatarUrl = $attributes['avatarUrl'];
    $avatarUrl = is_string($avatarUrl) ? $avatarUrl : null;
    if (
      $this->settings->get('liplum-sync-profile-core.sync-avatar', false)
      && isset($avatarUrl)
    ) {
      $ignoreUnchangedAvatar = $this->settings->get('liplum-sync-profile-core.ignore-unchanged-avatar', true);
      $newHash = hash("md5", $user->avatar_url . '+' . $avatarUrl);
      if (
        !$ignoreUnchangedAvatar ||
        $newHash != $user->last_avatar_hash
      ) {
        try {
          $image = (new ImageManager())->make($avatarUrl);
        } catch (\Exception $e) {
          $this->log->error("Sync avatar failed. $e");
          return;
        }
        $this->avatarUploader->upload($user, $image);
      }
      // update new hash
      $user->last_avatar_hash = hash("md5", $user->avatar_url . '+' . $avatarUrl);
    }
  }

  private function syncGroup(User $user, $attributes)
  {
    // If groups present and groups sync enabled
    $groups = $attributes['groups'];
    $groups = $groups ? (is_array($groups) ? $groups : null) : null;
    if (
      isset($groups)
      && $this->settings->get('liplum-sync-profile-core.sync-groups', false)
    ) {
      $newGroupIds = [];
      foreach ($groups as $group) {
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
  }

  private function syncFofMasquerade(User $user, $attributes){
    // If fof-masquerade present and masquerade sync enabled
    $fofMasquerade = $attributes['fof/masquerade'];
    $fofMasquerade = is_array($fofMasquerade) ? $fofMasquerade : null;
    if (
      isset($fofMasquerade)
      && $this->settings->get('liplum-sync-profile-core.sync-masquerade', false)
    ) {
      if (!$this->extensions->isEnabled('fof-masquerade')) {
        $this->log->debug("Profile sync of of-masquerade failed, because extension 'fof/masquerade' is not enabled.");
      } else {
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
          return;
        }
      }
    }
  }

  public function sync(User $user, $attributes)
  {
    $email = $user->email;

    $this->syncNickname($user, $attributes);
    $this->syncBio($user, $attributes);
    $this->syncAvatar($user, $attributes);
    $this->syncGroup($user, $attributes);
    $this->syncFofMasquerade($user, $attributes);
  }
}
