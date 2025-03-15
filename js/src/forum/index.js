import UserCard from 'flarum/forum/components/UserCard'
import app from 'flarum/forum/app'
import { override } from 'flarum/common/extend'

app.initializers.add(extName, () => {
  override(UserCard.prototype, 'view', function (original) {
    if (app.forum.attribute(`${extName}.block-profile-changes`)) {
      this.attrs.editable = false
    }

    return original()
  })
})
