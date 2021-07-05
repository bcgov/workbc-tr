<?php

/**
 * @file
 * Views hook implementations for the Search API spellcheck module.
 */

/**
 * Implements hook_views_data().
 */
function search_api_spellcheck_views_data() {
  // @todo: Only enable for search api views.
  $data['views']['search_api_spellcheck_did_you_mean'] = [
    'title' => t('Search API Spellcheck "Did You Mean"'),
    'help' => t('Did you mean suggestion for spellings'),
    'area' => [
      'id' => 'search_api_spellcheck_did_you_mean',
    ],
  ];
  $data['views']['search_api_spellcheck_suggetions'] = [
    'title' => t('Search API Spellcheck "Suggestions"'),
    'help' => t('Suggestions for spellings'),
    'area' => [
      'id' => 'search_api_spellcheck_suggestions',
    ],
  ];

  return $data;
}
