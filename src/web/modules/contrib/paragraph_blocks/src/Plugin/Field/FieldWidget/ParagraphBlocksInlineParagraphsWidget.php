<?php

namespace Drupal\paragraph_blocks\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Plugin\Field\FieldWidget\InlineParagraphsWidget;

/**
 * Extend the inline paragraphs widget.
 *
 * Used to change the summary display to the admin title; and to hide the
 * admin title on embedded paragraphs (paragraphs within paragraphs). Only
 * top level paragraphs use the admin title.
 */
class ParagraphBlocksInlineParagraphsWidget extends InlineParagraphsWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // Hide admin_label when this is not a top level paragraph. It is not a
    // top level paragraph if there are multiple subforms.
    $subform = &$element['subform'];
    if (isset($subform['#parents']) && count(array_intersect($subform['#parents'], ['subform'])) > 1) {
      $subform['admin_title']['#access'] = FALSE;
    }

    return $element;
  }

}
