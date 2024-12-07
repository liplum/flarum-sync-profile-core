# Flarum Sync Profile Core

A [Flarum](http://flarum.org) extension to sync user profile(attributes) when authenticated by an external identity provider. This extension provides support for syncing:

- Nickname
- Avatar
- Groups
- Bio
- Masquerade Attributes

Some authentication protocols, such as SAML2, LDAP, OpenID Connect, etc have the ability to send attributes along with an authentication response. This extension provides a framework for syncing user attributes and permissions via that attribute response.

## Get Started

Installation the extension:

```sh
composer require liplum/flarum-sync-profile-core
```

Update the extension:

```sh
composer update liplum/flarum-sync-profile-core
```

## How to Use

## For Flarum Admins

If you're using an authentication or auth-sync extension based on this extension
you can choose which types of attributes you'd like to sync on the extension settings page.

Note that in order to sync Bios and Masquerade Profile Fields, you need to install and enable the corresponding extensions:

- [Friends of Flarum User Bios](https://github.com/FriendsOfFlarum/user-bio)
- [Friends of Flarum Masquerade](https://github.com/FriendsOfFlarum/masquerade)

## For Extension Developers

To develop an authentication or auth-sync extension based on this extension, you should:

1. Install the extension as dev dependency: `composer require liplum/sync-profile-core`.
2. Inject `ExtensionManager $extensions` in your constructor.
3. Import the AuthEventSync model via `use Liplum\SyncProfile\Event\SyncProfileEvent;`
4. Before logging in/registering the user, create an AuthSyncEvent. Ex:

```php
use Flarum\Extension\ExtensionManager;
use Illuminate\Contracts\Events\Dispatcher;

private ExtensionManager $extensions;
private Dispatcher $dispatcher;

if ($this->extensions->isEnabled('liplum-sync-profile-core')) {
  $this->dispatcher->dispatch(new SyncProfileEvent(
    "example.user@example.com",[
      "avatarUrl" => "https://example.com/avatar.jpg",
      "nickname" => "Test User",
      "bio" => "Hello, this is my bio",
      "groups" => [1, 15, 2],
      "fof/masquerade" => [
          "fieldA" => "Example A",
          "fieldB" => "Example B",
      ],
  ]));
}
```

Replace the generic values above with attributes from your Identity Provider's response.

Attributes should be provided in a json-encoded string (see above example). The JSON string should have the following attributes:

- `nickname`: The display name of a user.
- `avatarUrl`: A URL pointing to an image for the user's avatar. Make sure that the file type is compatible with Flarum (jpeg or png I believe).
- `groups`: A comma-separated list of ids for groups that a user should belong to. Keep in mind that this will both add and remove groups, so make sure that all desired groups are included.
- `bio`: A string that will be synced to the user's bio if [Friends of Flarum User Bios](https://github.com/FriendsOfFlarum/user-bio) is enabled
- `fof-masquerade`: An associative array for any masquerade keys and attributes you want to sync. Make sure that the key matches the name of the profile field exactly.

### TODO

- Add better validation and error handling.
- Add a setting to support default groups that all users should be added to, and groups that users should never be synced to.
- Add support for getting users via LoginProvider providers and identifiers, in addition to email.

## Acknowledgement

Thanks to <https://github.com/askvortsov1/flarum-auth-sync> with `MIT License`.
