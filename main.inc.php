<?php
/*
Plugin Name: Relay Albums
Version: auto
Description: Relay albums into other albums for cross-presentations
Plugin URI: auto
Author: Laurent PROTT
Author URI: https://laurepro.fr
Has Settings: false
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

if (basename(dirname(__FILE__)) != 'RelayAlbums') {
  add_event_handler('init', 'relayalbums_error');
  function RelayAlbums_error()
  {
    global $page;
    $page['errors'][] = 'RelayAlbums folder name is incorrect, uninstall the plugin and rename it to "RelayAlbums"';
  }
  return;
}

global $prefixeTable;

define('RELAY_PATH',  PHPWG_PLUGINS_PATH . 'RelayAlbums/');
define('RELAY_ADMIN', get_root_url() . 'admin.php?page=plugin-RelayAlbums');
define('CATEGORY_RELAY_TABLE', $prefixeTable . 'category_relay');

add_event_handler('init', 'relay_init');
add_event_handler('loc_begin_index_category_thumbnails_query', 'relay_loc_begin_index_category_thumbnails_query');
/**
 * TODO This event does not exist, it must be added in
 * 
 * 
 */
add_event_handler('user_cache_categories_table', 'relay_user_cache_categories_table');

if (defined('IN_ADMIN')) {
  include_once(RELAY_PATH . 'include/events_admin.inc.php');
  add_event_handler('delete_categories', 'relay_delete_categories');
  add_event_handler('tabsheet_before_select', 'relay_tabsheet_before_select', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);
  add_event_handler('invalidate_user_cache', 'relay_all_representative_picture');
}

include_once(RELAY_PATH . 'include/events.inc.php');

/**
 * update plugin & unserialize conf & load language
 */
function relay_init()
{
  if (defined('IN_ADMIN')) {
    load_language('plugin.lang', RELAY_PATH);
  }
}
