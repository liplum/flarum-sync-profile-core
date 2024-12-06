<?php

namespace Liplum\SyncProfile\Listener;

use Liplum\SyncProfile\Models\SyncProfileEventModel;
use Liplum\SyncProfile\Event\SyncProfileEvent;

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
use Flarum\Foundation\Config;
use Psr\Log\LoggerInterface;

class SyncProfileEventListener
{
  protected $avatarUploader;
  protected $extensions;
  protected $settings;
  protected $container;
  protected $config;

  public function __construct(Container $container, AvatarUploader $avatarUploader, ExtensionManager $extensions, SettingsRepositoryInterface $settings, Config $config)
  {
    $this->avatarUploader = $avatarUploader;
    $this->extensions = $extensions;
    $this->settings = $settings;
    $this->container = $container;
    $this->config = $config;
  }

  public function subscribe(Dispatcher $events)
  {
    $events->listen(Registered::class, [$this, 'syncModel']);
    $events->listen(LoggedIn::class, [$this, 'syncModel']);
    $events->listen(SyncProfileEvent::class, [$this, 'syncEvent']);
  }

  public function syncModel($event)
  {
    $user = $event->user;
    $events = SyncProfileEventModel::where('email', $user->email)->orderBy('time', 'asc')->get();
    foreach ($events as $event) {
      $this->sync($event->user, $event->attributes);
      $event->delete();
    }
    $user->save();
  }

  public function syncEvent(SyncProfileEvent $event)
  {
    $user = User::query()->where("email", $event->email)->first();
    if (!$user) return;
    $this->sync($user, $event->attributes);
    $user->save();
  }

  public function sync(User $user, $attributes)
  {
    $email = $user->email;
    $attributes = json_decode($attributes, true);
    // If nickname present and nickname sync enabled
    $nickname = $attributes["nickname"];
    if (
      isset($nickname)
      && $this->settings->get('liplum-sync-profile-core.sync-nickname', false)
      && $nickname != $user->$nickname
    ) {
      if (!$this->extensions->isEnabled('flarum/nicknames')) {
        $this->debugLog("Extension 'flarum/nicknames' is not enabled.");
      }
      $user->nickname = $nickname;
    }

    // If bio present and bio sync enabled
    $bio = $attributes["bio"];
    $bio = $bio ? is_string($bio) : null;
    if (
      $this->settings->get('liplum-sync-profile-core.sync-bio', false)
      && isset($bio)
      && $bio != $user->bio
    ) {
      if (!$this->extensions->isEnabled('fof-user-bio')) {
        $this->debugLog("Extension 'fof/user-bio' is not enabled.");
      }
      $user->bio = $bio;
    }

    // If avatar present and avatar sync enabled
    $avatar = $attributes['avatar'];
    if (
      $this->settings->get('liplum-sync-profile-core.sync-avatar', false)
      && isset($avatar)
    ) {
      $image = (new ImageManager())->make($avatar);
      $this->avatarUploader->upload($user, $image);
    }

    // If groups present and groups sync enabled
    $groups = $attributes['groups'];
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

    // If fof-masquerade present and masquerade sync enabled
    $fofMasquerade = $attributes['fof/masquerade'];
    $fofMasquerade = is_array($fofMasquerade) ? $fofMasquerade : null;
    if (
      isset($fofMasquerade)
      && $this->settings->get('liplum-sync-profile-core.sync-masquerade', false)
    ) {
      if (!$this->extensions->isEnabled('fof-masquerade')) {
        $this->debugLog("Profile sync of of-masquerade failed, because extension 'fof/masquerade' is not enabled.");
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
        }
      }
    }
  }

  protected function debugLog(string $message)
  {
    if ($this->config->inDebugMode()) {
      /**
       * @var $logger LoggerInterface
       */
      $logger = resolve(LoggerInterface::class);
      $logger->info($message);
    }
  }
}
