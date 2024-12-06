import app from 'flarum/admin/app';

app.initializers.add('liplum/sync-profile-core', () => {
  app.extensionData
    .for('liplum-sync-profile-core')
    .registerSetting({
      setting: 'liplum-sync-profile-core.sync-nickname',
      label: app.translator.trans('liplum-sync-profile-core.admin.sync-nickname.label'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.sync-avatar',
      label: app.translator.trans('liplum-sync-profile-core.admin.sync-avatar.label'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.block-avatar-change',
      label: app.translator.trans('liplum-sync-profile-core.admin.block-avatar-change.label'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.sync-groups',
      label: app.translator.trans('liplum-sync-profile-core.admin.sync-groups.label'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.sync-bio',
      label: app.translator.trans('liplum-sync-profile-core.admin.sync-bio.label'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.block-bio-change',
      label: app.translator.trans('liplum-sync-profile-core.admin.block-bio-change.label'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'liplum-sync-profile-core.sync-fof-masquerade',
      label: app.translator.trans('liplum-sync-profile-core.admin.sync-fof-masquerade.label'),
      type: 'boolean'
    })
});
