import UserCard from 'flarum/forum/components/UserCard'
import app from 'flarum/forum/app'
import { extend, override } from 'flarum/common/extend'

app.initializers.add('liplum/sync-profile-core', () => {
  override(UserCard.prototype, 'view', function (original) {
    if (app.forum.attribute('blockAvatarChange')) {
      this.attrs.editable = false
    }

    return original()
  })


  extend(UserCard.prototype, 'oncreate', function () {
    if (app.forum.attribute('blockBioChange') && $('.UserBio').hasClass('editable')) {
      if ($('.UserBio-content').find('.UserBio-placeholder').length !== 0) {
        var styleTag = $('<style>.item-bio { display: none !important; }</style>')
        $('html > head').append(styleTag)
      } else {
        $('.UserBio').clone()
          .off()
          .appendTo('.item-bio')
        $('.UserBio').first().remove()
        $('.UserBio').removeClass('editable')
      }
    }
  })
})
