app.initializers.add('askvortsov/auth-sync', () => {
    app.extensionData
        .for('askvortsov-auth-sync')
        .registerSetting({
            setting: 'askvortsov-auth-sync.sync_avatar',
            label: app.translator.trans('askvortsov-auth-sync.admin.labels.sync_avatar'),
            type: 'boolean'
        })
        .registerSetting({
            setting: 'askvortsov-auth-sync.ignored_avatar',
            label: app.translator.trans('askvortsov-auth-sync.admin.labels.ignored_avatar'),
            type: 'text'
        })
        .registerSetting({
            setting: 'askvortsov-auth-sync.stop_avatar_change',
            label: app.translator.trans('askvortsov-auth-sync.admin.labels.stop_avatar_change'),
            type: 'boolean'
        })
        .registerSetting({
            setting: 'askvortsov-auth-sync.sync_groups',
            label: app.translator.trans('askvortsov-auth-sync.admin.labels.sync_groups'),
            type: 'boolean'
        })
        .registerSetting({
            setting: 'askvortsov-auth-sync.sync_bio',
            label: app.translator.trans('askvortsov-auth-sync.admin.labels.sync_bio'),
            type: 'boolean'
        })
        .registerSetting({
            setting: 'askvortsov-auth-sync.stop_bio_change',
            label: app.translator.trans('askvortsov-auth-sync.admin.labels.stop_bio_change'),
            type: 'boolean'
        })
        .registerSetting({
            setting: 'askvortsov-auth-sync.sync_masquerade',
            label: app.translator.trans('askvortsov-auth-sync.admin.labels.sync_masquerade'),
            type: 'boolean'
        })
});