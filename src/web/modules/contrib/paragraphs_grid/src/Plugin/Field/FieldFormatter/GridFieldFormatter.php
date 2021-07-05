<?php

namespace Drupal\paragraphs_grid\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'grid_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "grid_field_formatter",
 *   label = @Translation("Grid field formatter"),
 *   field_types = {
 *     "grid_field_type"
 *   }
 * )
 */
class GridFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    return [];
  }

}
