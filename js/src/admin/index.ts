import app from 'flarum/admin/app';

app.initializers.add('liplum/sync-profile-core', () => {
  app.extensionData
    .for('liplum-sync-profile-core')
    .registerSetting({
      setting: 'liplum-sync-profile-core.sync_avatar',
      label: app.translator.trans('liplum-sync-profile-core.admin.labels.sync_avatar'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.ignored_avatar',
      label: app.translator.trans('liplum-sync-profile-core.admin.labels.ignored_avatar'),
      type: 'text'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.stop_avatar_change',
      label: app.translator.trans('liplum-sync-profile-core.admin.labels.stop_avatar_change'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.sync_groups',
      label: app.translator.trans('liplum-sync-profile-core.admin.labels.sync_groups'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.sync_bio',
      label: app.translator.trans('liplum-sync-profile-core.admin.labels.sync_bio'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.stop_bio_change',
      label: app.translator.trans('liplum-sync-profile-core.admin.labels.stop_bio_change'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.sync_masquerade',
      label: app.translator.trans('liplum-sync-profile-core.admin.labels.sync_masquerade'),
      type: 'boolean'
    })
});