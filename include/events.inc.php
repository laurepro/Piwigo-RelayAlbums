<?php
defined('RELAY_PATH') or die('Hacking attempt!');


/**
 * change query for relay album
 */
function relay_loc_begin_index_category_thumbnails_query($query)
{
    global $page;

    /* category page */
    if (isset($page['category'])) {
        $query = str_replace(
            'AND id_uppercat = ' . $page['category']['id'],
            'AND (id_uppercat = ' . $page['category']['id'] . ' OR id IN (SELECT child_id FROM ' . CATEGORY_RELAY_TABLE . ' WHERE parent_id = ' . $page['category']['id'] . '))',
            $query
        );
    }
    // echo '<pre>'.print_r($query,true).'</pre>';
    return $query;
}

/**
 * action on loc index
 */
function relay_user_cache_categories_table($user_cache_cats): array
{
    $query = '
    SELECT 
      C.id AS cat_id, 
      C.uppercats AS uppercats, 
      COUNT(R.child_id) AS count_relay, 
      GROUP_CONCAT(child_id) AS relays_id 
      FROM (' . CATEGORY_RELAY_TABLE . ' R, ' . CATEGORIES_TABLE  . ' C)
      WHERE R.parent_id = C.id AND R.parent_id IN(' . implode(',', array_keys($user_cache_cats)) . ')
      GROUP BY C.id
    ;';
    $result = pwg_query($query);

    while ($row = pwg_db_fetch_assoc($result)) {
        foreach ($user_cache_cats as $key => $cache_cat) {
            if ($cache_cat['cat_id'] == $row['cat_id']) {
                $user_cache_cats[$key]['nb_categories'] += $row['count_relay'];
            }
            if (in_array($cache_cat['cat_id'], explode(",", $row['uppercats']))) {
                $user_cache_cats[$key]['count_categories'] += $row['count_relay'];
                foreach (explode(',', $row['relays_id']) as $relay_id) {
                    $user_cache_cats[$key]['count_images'] += $user_cache_cats[$relay_id]['count_images'];
                }
            }
        }
    }

    // second pass case of relay of relay
    foreach ($user_cache_cats as $key => $cache_cat) {
        if ($user_cache_cats[$key]['count_images'] == 0 && $user_cache_cats[$key]['count_categories'] > 0) {
            $query = '
            SELECT child_id
              FROM ' . CATEGORY_RELAY_TABLE . '
              WHERE parent_id = ' . $cache_cat['cat_id'] . '
            ;';
            $result = pwg_query($query);
            while ($row = pwg_db_fetch_assoc($result)) {
                $user_cache_cats[$key]['count_images'] += $user_cache_cats[$row['child_id']]['count_images'];
            }
        }
    }

    return $user_cache_cats;
}
