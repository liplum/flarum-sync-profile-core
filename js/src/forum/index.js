import UserCard from 'flarum/forum/components/UserCard'
import app from 'flarum/forum/app'
import { override } from 'flarum/common/extend'

app.initializers.add('liplum-sync-profile-core', () => {
  override(UserCard.prototype, 'view', function (original) {
    if (app.forum.attribute('liplum-sync-profile-core.block-profile-changes')) {
      this.attrs.editable = false
    }

    return original()
  })
})
