<?php

namespace Drupal\search_api_spellcheck\Plugin\views\area;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\search_api\Plugin\views\filter\SearchApiFulltext;
use Drupal\search_api\Plugin\views\query\SearchApiQuery;
use Drupal\search_api\Query\ResultSetInterface;
use Drupal\views\Plugin\views\area\AreaPluginBase;

/**
 * Provides an area for messages.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("search_api_spellcheck_did_you_mean")
 */
class DidYouMeanSpellCheck extends AreaPluginBase {

  /**
   * The current query parameters.
   *
   * @var array
   */
  protected $currentQuery;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['search_api_spellcheck_count']['default'] = 1;
    $options['search_api_spellcheck_hide_on_result']['default'] = TRUE;
    $options['search_api_spellcheck_collate']['default'] = TRUE;
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['search_api_spellcheck_hide_on_result'] = [
      '#default_value' => $this->options['search_api_spellcheck_hide_on_result'] ?? TRUE,
      '#title' => $this->t('Hide when the view has results.'),
      '#type' => 'checkbox',
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @throws \InvalidArgumentException
   */
  public function query() {
    if (
      $this->query instanceof SearchApiQuery &&
      $this->query->getIndex()->getServerInstance()->supportsFeature('search_api_spellcheck')
    ) {
      $keys = $this->query->getKeys();
      // Don't set the option if $keys is NULL.
      if ($keys) {
        if (!is_array($keys)) {
          throw new \InvalidArgumentException('The selected parse mode for fulltext fields is not compatible to Search API Spellcheck.');
        }
        $this->query->setOption('search_api_spellcheck', [
          // Strip non numeric array keys like '#collation'.
          'keys' => array_filter($keys, 'is_int', ARRAY_FILTER_USE_KEY),
          // This parameter specifies the maximum number of suggestions that the
          // spellchecker should return for a term.
          'count' => $this->options['search_api_spellcheck_count'],
          // If true and the backend supports it, this parameter directs the
          // backend to take the best suggestion for each token (if one exists)
          // and construct a new query from the suggestions. For example, if the
          // input query was "jawa class lording" and the best suggestion for
          // "jawa" was "java" and "lording" was "loading", then the resulting
          // collation would be "java class loading".
          'collate' => $this->options['search_api_spellcheck_collate'],
        ]);
      }
    }
    parent::query();
  }

  /**
   * Render the area.
   *
   * @param bool $empty
   *   (optional) Indicator if view result is empty or not. Defaults to FALSE.
   *
   * @return array
   *   In any case we need a valid Drupal render array to return.
   */
  public function render($empty = FALSE) {
    if (!$this->options['search_api_spellcheck_hide_on_result'] || $empty) {
      /** @var ResultSetInterface $result */
      $result = $this->query->getSearchApiResults();
      if ($spellcheck = $result->getExtraData('search_api_spellcheck')) {
        $filter_field_key = $this->getFilterFieldKey();
        $exposed_input = $this->view->getExposedInput();
        $keys = $exposed_input[$filter_field_key] ?? '';
        $new_keys = $spellcheck['collation'] ?? $keys;

        if (empty($spellcheck['collation']) && !empty($spellcheck['suggestions'])) {
          // Loop over the suggestions and replace the keys.
          foreach ($spellcheck['suggestions'] as $key => $values) {
            $new_keys = str_ireplace($key, $values[0], $new_keys);
          }
        }

        // Don't offer the identical search keys as "Did you mean".
        if ($new_keys !== $keys) {
          return [
            '#theme' => 'search_api_spellcheck_did_you_mean',
            '#label' => $this->getSuggestionLabel(),
            '#link' => $this->getSuggestionLink($new_keys, $filter_field_key),
          ];
        }
      }
    }
    return [];
  }

  /**
   * Gets the current query parameters.
   *
   * @return array
   *   Key value of parameters.
   */
  protected function getCurrentQuery() {
    if (NULL === $this->currentQuery) {
      $this->currentQuery = \Drupal::request()->query->all();
    }
    return $this->currentQuery;
  }

  /**
   * Gets the filter field key for the current view.
   * Having multiple full text search filters are currently not supported.
   *
   * @return string
   *   The filter field key.
   */
  protected function getFilterFieldKey() {
    $field_key = null;

    $filters = $this->view->filter;
    foreach ($filters as $filter) {
      if (!isset($field_key)) {
        if ($filter instanceof SearchApiFulltext && $filter->isExposed()) {
          $exposed_info = $filter->exposedInfo();
          $field_key = $exposed_info['value'];
        }
      }
    }

    return $field_key;
  }

  /**
   * Gets the suggestion label.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The suggestion label translated.
   */
  protected function getSuggestionLabel() {
    return $this->t('Did you mean:');
  }

  /**
   * Gets the suggestion link.
   *
   * @param string $new_keys
   *   The suggestion.
   * @param string $filter_name
   *   The parameter name of text search filter.
   *
   * @return \Drupal\Core\Link
   *   The suggestion link.
   */
  protected function getSuggestionLink($new_keys, $filter_name) {
    $url = Url::fromRoute(
      '<current>',
      [$filter_name => $new_keys] + $this->getCurrentQuery()
    );

    return Link::fromTextAndUrl($new_keys, $url);
  }
}
