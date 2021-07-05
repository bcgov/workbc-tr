<?php

/**
 * @file
 * Provide views data for tracker.module.
 */

/**
 * Implements hook_views_data().
 */
function sharethis_views_data() {
  $data = [];
  // ShareThis link.
  $data['node']['sharethis_node'] = [
    'title' => t('ShareThis Link'),
    'help' => t('Link provided by the ShareThis service.'),
    'field' => [
      'id' => 'sharethis_node',
      'help' => t('Show Sharethis for a particular node.'),

    ],
  ];
  return $data;
}
