(()=>{var e={n:l=>{var r=l&&l.__esModule?()=>l.default:()=>l;return e.d(r,{a:r}),r},d:(l,r)=>{for(var n in r)e.o(r,n)&&!e.o(l,n)&&Object.defineProperty(l,n,{enumerable:!0,get:r[n]})},o:(e,l)=>Object.prototype.hasOwnProperty.call(e,l)};(()=>{"use strict";const l=flarum.core.compat["admin/app"];var r=e.n(l);r().initializers.add("liplum-sync-profile-core",(function(){r().extensionData.for("liplum-sync-profile-core").registerSetting({setting:"liplum-sync-profile-core.block-profile-changes",label:r().translator.trans("liplum-sync-profile-core.admin.block-profile-changes.label"),help:r().translator.trans("liplum-sync-profile-core.admin.block-profile-changes.help"),type:"boolean"}).registerSetting({setting:"liplum-sync-profile-core.sync-nickname",label:r().translator.trans("liplum-sync-profile-core.admin.sync-nickname.label"),type:"boolean"}).registerSetting({setting:"liplum-sync-profile-core.sync-avatar",label:r().translator.trans("liplum-sync-profile-core.admin.sync-avatar.label"),type:"boolean"}).registerSetting({setting:"liplum-sync-profile-core.sync-groups",label:r().translator.trans("liplum-sync-profile-core.admin.sync-groups.label"),type:"boolean"}).registerSetting({setting:"liplum-sync-profile-core.sync-bio",label:r().translator.trans("liplum-sync-profile-core.admin.sync-bio.label"),help:r().translator.trans("liplum-sync-profile-core.admin.sync-bio.help"),type:"boolean"}).registerSetting({setting:"liplum-sync-profile-core.sync-fof-masquerade",label:r().translator.trans("liplum-sync-profile-core.admin.sync-fof-masquerade.label"),help:r().translator.trans("liplum-sync-profile-core.admin.sync-fof-masquerade.help"),type:"boolean"})}))})(),module.exports={}})();
//# sourceMappingURL=admin.js.map