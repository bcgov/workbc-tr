<?php

namespace Drupal\paragraph_blocks\Entity;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Extend the Paragraph entity.
 */
class ParagraphBlocksEntity extends Paragraph {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getSummary(array $options = []) {
    // Get any field with title in the name.
    $value = $this->admin_title->getValue();
    foreach ($this->getFieldDefinitions() as $field_name => $field_definition) {
      if (strpos($field_name, 'title') !== FALSE) {
        $text = $this->getTextSummary($field_name, $field_definition);
        if (!empty($text)) {
          return $text;
        }
      }
    }
    return parent::getSummary($options);
  }

}
