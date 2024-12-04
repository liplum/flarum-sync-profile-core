import app from 'flarum/admin/app';

app.initializers.add('liplum/sync-profile', () => {
  app.extensionData
    .for('liplum-sync-profile')
    .registerSetting({
      setting: 'liplum-sync-profile.sync_avatar',
      label: app.translator.trans('liplum-sync-profile.admin.labels.sync_avatar'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile.ignored_avatar',
      label: app.translator.trans('liplum-sync-profile.admin.labels.ignored_avatar'),
      type: 'text'
    })
    .registerSetting({
      setting: 'liplum-sync-profile.stop_avatar_change',
      label: app.translator.trans('liplum-sync-profile.admin.labels.stop_avatar_change'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile.sync_groups',
      label: app.translator.trans('liplum-sync-profile.admin.labels.sync_groups'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile.sync_bio',
      label: app.translator.trans('liplum-sync-profile.admin.labels.sync_bio'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile.stop_bio_change',
      label: app.translator.trans('liplum-sync-profile.admin.labels.stop_bio_change'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile.sync_masquerade',
      label: app.translator.trans('liplum-sync-profile.admin.labels.sync_masquerade'),
      type: 'boolean'
    })
});