# Flarum Sync Profile

A [Flarum](http://flarum.org) extension to sync user attributes when authenticated by an external identity provider. This extension provides support for syncing:

- Avatar
- Groups
- Bio
- Masquerade Attributes

Some authentication protocols, such as SAML2, LDAP, OpenID Connect, etc have the ability to send attributes along with an authentication response. This extension provides a framework for syncing user attributes and permissions via that attribute response.

## Installation

Installation the extension:

```sh
composer require liplum/flarum-sync-profile
```

Update the extension:

```sh
composer update liplum/flarum-sync-profile
```

## How to Use

For flarum administrators, if you're using an authentication extension that uses this extension, you can use the settings popup on the Flarum Admin extensions page to choose which types of attributes you'd like to sync. Please note that in order to sync Bios and Masquerade Profile Fields, you need to install and enable the [Friends of Flarum User Bios](https://github.com/FriendsOfFlarum/user-bio) and [Friends of Flarum Masquerade](https://github.com/FriendsOfFlarum/masquerade) extensions.

To use this extension in another authentication extension:

- Import the AuthEventSync model via `use Liplum\SyncProfile\Models\AuthSyncEvent;`
- Before logging in/registering the user, create an AuthSyncEvent. Ex:

```php
use Liplum\SyncProfile\Models\AuthSyncEvent;
use Carbon\Carbon;

if ($this->extensions->isEnabled('liplum-sync-profile') && $this->settings->get('liplum-saml.sync_attributes', '')) {
    $event = new AuthSyncEvent();
    $event->email="example.user@example.com";
    $event->attributes = json_encode([
        "avatar" => "https://example.com/avatar.jpg",
        "bio" => "Hello, this is my bio",
        "groups" => [1, 15, 2],
        "masquerade_attributes" => [
            "First Name" => "Example",
            "Last Name" => "User",
            "Website" => "https://example.com
        ]
    ]);
    $event->time = Carbon::now();
    $event->save();
}
```

- Of course, replace the generic values above with attributes from your Identity Provider's response.
- Attributes should be provided in a json-encoded string (see above example). The JSON string should have the following attributes:
  - `avatar`: A URL pointing to an image for the user's avatar. Make sure that the file type is compatible with Flarum (jpeg or png I believe).
  - `groups`: A comma-separated list of ids for groups that a user should belong to. Keep in mind that this will both add and remove groups, so make sure that all desired groups are included.
  - `bio`: A string that will be synced to the user's bio if [Friends of Flarum User Bios](https://github.com/FriendsOfFlarum/user-bio) is enabled
  - `masquerade_attributes`: An associative array for any masquerade keys and attributes you want to sync. Make sure that the key matches the name of the profile field exactly.

### TODO

- Add better validation and error handling.
- Add a setting to support default groups that all users should be added to, and groups that users should never be synced to.
- Add an expiry setting for Auth Sync Events.
- Add support for getting users via LoginProvider providers and identifiers, in addition to email.
- Due to a bug in flarum core, the `LoggedIn` event isn't dispatched when logging in via an external identity provider. Until this is fixed, the UserUpdatedListener listens to `Serialize` events (except those going to masquerade). This workaround will be promptly removed when the upstream bug is fixed.

## Acknowledgement

Thanks to <https://github.com/askvortsov1/flarum-auth-sync> with `MIT License`.
