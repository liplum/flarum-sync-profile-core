(()=>{var e={n:o=>{var t=o&&o.__esModule?()=>o.default:()=>o;return e.d(t,{a:t}),t},d:(o,t)=>{for(var r in t)e.o(t,r)&&!e.o(o,r)&&Object.defineProperty(o,r,{enumerable:!0,get:t[r]})},o:(e,o)=>Object.prototype.hasOwnProperty.call(e,o)};(()=>{"use strict";const o=flarum.core.compat["forum/components/UserCard"];var t=e.n(o);const r=flarum.core.compat["forum/app"];var a=e.n(r);const n=flarum.core.compat["common/extend"];a().initializers.add("liplum/sync-profile-core",(function(){(0,n.override)(t().prototype,"view",(function(e){return a().forum.attribute("blockAvatarChange")&&(this.attrs.editable=!1),e()})),(0,n.extend)(t().prototype,"oncreate",(function(){if(a().forum.attribute("blockBioChange")&&$(".UserBio").hasClass("editable"))if(0!==$(".UserBio-content").find(".UserBio-placeholder").length){var e=$("<style>.item-bio { display: none !important; }</style>");$("html > head").append(e)}else $(".UserBio").clone().off().appendTo(".item-bio"),$(".UserBio").first().remove(),$(".UserBio").removeClass("editable")}))}))})(),module.exports={}})();
//# sourceMappingURL=forum.js.map