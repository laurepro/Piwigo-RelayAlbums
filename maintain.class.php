<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class RelayAlbums_maintain extends PluginMaintain
{
  private $table;
  private $default_conf = array();

  function __construct($plugin_id)
  {
    global $prefixeTable;

    parent::__construct($plugin_id);
    $this->table = $prefixeTable . 'category_relay';
  }

  function install($plugin_version, &$errors = array())
  {
    // new table
    pwg_query(
      'CREATE TABLE IF NOT EXISTS `' . $this->table . '` (
  `parent_id` smallint(5) unsigned NOT NULL,
  `child_id` smallint(5) unsigned NOT NULL,
  CONSTRAINT UC_relay UNIQUE (`parent_id`,`child_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;'
    );

  }

  function update($old_version, $new_version, &$errors = array())
  {
    $this->install($new_version, $errors);
  }

  function uninstall()
  {
    pwg_query('DROP TABLE `' . $this->table . '`;');
  }
}
