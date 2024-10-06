<?php
defined('RELAY_PATH') or die('Hacking attempt!');

global $conf, $template, $page;

$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : 'cat_list';

if ($page['tab'] == 'album')
{
  include(RELAY_PATH . 'admin/album.php');
}

$template->assign(array(
  'RELAY_PATH' => RELAY_PATH,
  'RELAY_ABS_PATH' => realpath(RELAY_PATH) . '/',
  ));

$template->assign_var_from_handle('ADMIN_CONTENT', 'RelayAlbums_content');
