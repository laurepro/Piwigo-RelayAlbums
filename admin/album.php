<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
defined('RELAY_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Basic checks                                                          |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

$page['active_menu'] = get_active_menu('album');

check_input_parameter('cat_id', $_GET, false, PATTERN_ID);
$cat_id = $_GET['cat_id'];

$admin_album_base_url = get_root_url() . 'admin.php?page=album-' . $cat_id;
$self_url = RELAY_ADMIN . '-album&amp;cat_id=' . $cat_id;

$query = '
SELECT *
  FROM ' . CATEGORIES_TABLE . '
  WHERE id = ' . $cat_id . '
;';
$category = pwg_db_fetch_assoc(pwg_query($query));

if (!isset($category['id'])) {
  die("unknown album");
}

// category must be virtual
if ($category['dir'] != NULL) {
  die("physical album");
}


// +-----------------------------------------------------------------------+
// | Tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH . 'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('album');
$tabsheet->select('relayalbum');
$tabsheet->assign();


// +-----------------------------------------------------------------------+
// | Save Relays                                                          |
// +-----------------------------------------------------------------------+

if (isset($_POST['submitRelay'])) {
  if (defined('RELAY_DEBUG')) {
    var_dump($_POST['relay']);
  }

  /* delete old relays */
  pwg_query('
  DELETE FROM ' . CATEGORY_RELAY_TABLE . ' 
    WHERE parent_id = ' . $cat_id . ' OR child_id = ' . $cat_id . '
  ;');

  /* insert new relays */
  $inserts = $check_associations = array();
  if (isset(($_POST['relay']['parents']))) {
    foreach ($_POST['relay']['parents'] as $parent) {
      $inserts[] = ['parent_id' => $parent, 'child_id' => $cat_id];
      $check_associations[$parent] = true;
    }
  }
  if (isset(($_POST['relay']['children']))) {
    foreach ($_POST['relay']['children'] as $child) {
      $inserts[] = ['parent_id' => $cat_id, 'child_id' => $child];
      $check_associations[$cat_id] = true;
    }
  }
  
  if (count($inserts) > 0) {
    mass_inserts(
      CATEGORY_RELAY_TABLE,
      array('parent_id', 'child_id'),
      $inserts,
      array('ignore' => true)
    );

    $_SESSION['page_infos'] = array(l10n('RelayAlbums updated'));
  } else {
    $_SESSION['page_infos'] = array(l10n('RelayAlbums erased'));
  }

  invalidate_user_cache();
}

// +-----------------------------------------------------------------------+
// | Display page                                                          |
// +-----------------------------------------------------------------------+

/* get relay for this album */
$query = '
SELECT *
  FROM ' . CATEGORY_RELAY_TABLE . '
  WHERE parent_id = ' . $cat_id . ' OR child_id =  ' . $cat_id . '
;';
$result = pwg_query($query);

$parents = $children = array();
while ($relay = pwg_db_fetch_assoc($result)) {
  if ($relay['parent_id'] == $cat_id) {
    $children[] = $relay['child_id'];
  }
  if ($relay['child_id'] == $cat_id) {
    $parents[] = $relay['parent_id'];
  }
}

/* all albums */
$query = '
SELECT
    id,
    name,
    uppercats,
    global_rank
  FROM ' . CATEGORIES_TABLE . '
;';
display_select_cat_wrapper($query, array(), 'all_albums');
$template->assign('relay_parents_selected', $parents);
$template->assign('relay_children_selected', $children);


$template->assign(array(
  'CAT_ID' => $cat_id,
  'level_options' => get_privacy_level_options(),
  'F_ACTION' => $self_url,
  'ADMIN_PAGE_TITLE' => l10n('Edit album') . ' <strong>' . $category['name'] . '</strong>',
  'ADMIN_PAGE_OBJECT_ID' => '#' . $category['id'],
));

$template->set_filename('RelayAlbums_content', realpath(RELAY_PATH . 'admin/template/album.tpl'));
