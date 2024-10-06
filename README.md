# Piwigo RelayAlbums
Relay albums into other albums for cross-presentations
* Internal name: `RelayAlbums` (directory name in `plugins/`)

# Attention !

use non existant trigger_change

Add this line :
```
$user_cache_cats = trigger_change('user_cache_categories_table', $user_cache_cats);
```
in file *include\function_user.inc.php* after line 448

So, replace
```
447      // now we update user cache categories
448      $user_cache_cats = get_computed_categories($userdata, null);
449      if ( !is_admin($userdata['status']) )
450      { // for non admins we forbid categories with no image (feature 1053)
451        $forbidden_ids = array();
```
by
```
447      // now we update user cache categories
448      $user_cache_cats = get_computed_categories($userdata, null);
449      $user_cache_cats = trigger_change('user_cache_categories_table', $user_cache_cats);
450      if ( !is_admin($userdata['status']) )
451      { // for non admins we forbid categories with no image (feature 1053)
452        $forbidden_ids = array();
```
