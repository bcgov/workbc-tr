<?php

namespace Drupal\search_api_spellcheck\Plugin\views\area;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_api\Query\ResultSetInterface;

/**
 * Provides an area for messages.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("search_api_spellcheck_suggestions")
 */
class SuggestionsSpellCheck extends DidYouMeanSpellCheck {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['search_api_spellcheck_collate']['default'] = FALSE;
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['search_api_spellcheck_count'] = [
      '#default_value' => $this->options['search_api_spellcheck_count'] ?? TRUE,
      '#title' => $this->t('The amount of results to show.'),
      '#type' => 'number',
    ];
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

        $key_suggestions = [];
        if (empty($spellcheck['collation']) && !empty($spellcheck['suggestions'])) {
          $combined_suggestions[$new_keys] = $this->combineArrays(array_values($spellcheck['suggestions']));
          // Loop over the combined suggestions and replace the keys.
          foreach ($combined_suggestions as $key => $values) {
            foreach ($values as $value) {
              if (!empty($key)) {
                $key_suggestions[] = str_ireplace($key, $value, $new_keys);
              }
            }
          }
        }
        else {
          return [];
        }

        if (empty($key_suggestions)) {
          return [];
        }

        $suggestions = [];
        foreach ($key_suggestions as $suggestion) {
          $suggestions[] = $this->getSuggestionLink($suggestion, $filter_field_key);
        }

        return [
          '#theme' => 'search_api_spellcheck_suggestions',
          '#label' => $this->getSuggestionLabel(),
          '#suggestions' => $suggestions,
        ];
      }
    }
    return [];
  }

  /**
   * Gets the suggestion label.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The suggestion label translated.
   */
  protected function getSuggestionLabel() {
    return $this->t('Spellcheck keyword variations:');
  }

  /**
   * Combine multiple arrays to one array with all possible suggestions.
   */
  protected function combineArrays(array $suggestions) {
    $odometer = array_fill(0, count($suggestions), 0);
    $combined_suggestions[] = $this->formCombination($odometer, $suggestions);

    while ($this->odometerIncrement($odometer, $suggestions) ){
      $combined_suggestions[] = $this->formCombination( $odometer, $suggestions);
    }

    return $combined_suggestions;
  }

  /**
   * Combine a suggestion based on the odometer.
   */
  protected function formCombination(array $odometer, array $suggestions) {
    $output = '';
    $count = count($odometer);
    for ($i = 0; $i < $count; $i++) {
      if ($i === 0) {
        $output .= $suggestions[$i][$odometer[$i]];
      }
      else {
        $output .= ' ' . $suggestions[$i][$odometer[$i]];
      }
    }

    return $output;
  }

  /**
   * Increment the odometer.
   */
  protected function odometerIncrement(array &$odometer, array $suggestions) {

    // Basically, work your way from the rightmost digit of the "odometer"...
    // if you're able to increment without cycling that digit back to zero,
    // you're all done, otherwise, cycle that digit to zero and go one digit to
    // the left, and begin again until you're able to increment a digit without
    // cycling it.
    $count = count($odometer);
    for ($i = $count - 1; $i >= 0; $i--) {
      $max = count($suggestions[$i]) -1;

      if ($odometer[$i] + 1 <= $max) {
        // Increment and done.
        $odometer[$i]++;
        return TRUE;
      }

      if ($i - 1 < 0) {
        // No more digits left to increment, end of the line.
        break;
      }

      // Can't increment this digit, cycle it to zero and continue the loop
      // to go over to the next digit.
      $odometer[$i] = 0;
    }

    return FALSE;
  }
}
