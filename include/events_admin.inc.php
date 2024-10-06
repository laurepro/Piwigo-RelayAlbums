<?php
defined('RELAY_PATH') or die('Hacking attempt!');

/**
 * new tab on album properties page
 */
function relay_tabsheet_before_select($sheets, $id)
{
  if ($id == 'album') {
    global $category;

    if ($category['dir'] == null) {
      $sheets['relayalbum'] = array(
        'caption' => '<span class="icon-link"></span>Relay Album',
        'url' => RELAY_ADMIN . '-album&amp;cat_id=' . $_GET['cat_id'],
      );
    }
  }

  return $sheets;
}

/**
 * clean table when categories are deleted
 */
function relay_delete_categories($ids)
{
  pwg_query('
DELETE FROM ' . CATEGORY_RELAY_TABLE . '
  WHERE parent_id IN(' . implode(',', $ids) . ') OR child_id IN(' . implode(',', $ids) . ') 
;');
}

function relay_all_representative_picture()
{

  // get relay categories
  $query = '
SELECT DISTINCT parent_id
  FROM ' . CATEGORY_RELAY_TABLE . '
;';

  // regenerate photo list
  $relay_cats = query2array($query, null, 'parent_id');
  array_map('relay_representative_picture', $relay_cats);
}

function relay_representative_picture($cat_id)
{
  // get relay categories pictures ids
  $query = '
   SELECT DISTINCT I.image_id
     FROM (' . CATEGORY_RELAY_TABLE . ' R, ' . IMAGE_CATEGORY_TABLE . ' I)
     WHERE R.parent_id = ' . $cat_id . ' AND I.category_id = R.child_id
   UNION 
   SELECT representative_picture_id
     FROM (' . CATEGORY_RELAY_TABLE . ' R, ' . CATEGORIES_TABLE . ' C)
     WHERE R.parent_id = ' . $cat_id . ' AND C.id = R.child_id
   ;';
  $relay_images = query2array($query, null, 'image_id');

  // get relay uppercats
  $query = '
  SELECT uppercats 
    FROM ' . CATEGORIES_TABLE . '
    WHERE id = ' . $cat_id . '
  ;';
  $relay_uppercats = query2array($query, null, 'uppercats');

  // random set image to uppercats
  $query = '
  UPDATE ' . CATEGORIES_TABLE . ' 
    SET representative_picture_id = ' . $relay_images[array_rand($relay_images)] . ' 
    WHERE id IN (' . $relay_uppercats[0] . ')
      AND IFNULL(representative_picture_id,0) NOT IN (' . implode(',', $relay_images) . ')
  ;';
  pwg_query($query);
}
