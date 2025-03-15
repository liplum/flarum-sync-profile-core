import app from "flarum/admin/app";
import { extName } from "src/common";

app.initializers.add(extName, () => {
  app.extensionData
    .for(extName)
    .registerSetting({
      setting: `${extName}.block-profile-changes`,
      label: app.translator.trans(`${extName}.admin.block-profile-changes.label`),
      help: app.translator.trans(`${extName}.admin.block-profile-changes.help`),
      type: `boolean`
    })
    .registerSetting({
      setting: `${extName}.sync-nickname`,
      label: app.translator.trans(`${extName}.admin.sync-nickname.label`),
      type: `boolean`
    })
    .registerSetting({
      setting: `${extName}.sync-avatar`,
      label: app.translator.trans(`${extName}.admin.sync-avatar.label`),
      type: `boolean`
    })
    .registerSetting({
      setting: `${extName}.ignore-unchanged-avatar`,
      label: app.translator.trans(`${extName}.admin.ignore-unchanged-avatar.label`),
      help: app.translator.trans(`${extName}.admin.ignore-unchanged-avatar.help`),
      type: `boolean`
    })
    .registerSetting({
      setting: `${extName}.sync-groups`,
      label: app.translator.trans(`${extName}.admin.sync-groups.label`),
      type: `boolean`
    })
    .registerSetting({
      setting: `${extName}.sync-bio`,
      label: app.translator.trans(`${extName}.admin.sync-bio.label`),
      help: app.translator.trans(`${extName}.admin.sync-bio.help`),
      type: `boolean`
    })
    .registerSetting({
      setting: `${extName}.sync-fof-masquerade`,
      label: app.translator.trans(`${extName}.admin.sync-fof-masquerade.label`),
      help: app.translator.trans(`${extName}.admin.sync-fof-masquerade.help`),
      type: `boolean`
    })
});
